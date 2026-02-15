<?php

namespace App\Livewire\Tools\Balance;

use App\Models\ImportFile;
use App\Models\Queue\TrialBalanceData;
use App\Models\TrialBalanceDecision;
use App\Services\TrialBalanceAiPreviewStore;
use App\Services\TrialBalanceAiSuggester;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class ValidateTrialBalanceAiPreview extends Component
{
    public ImportFile $file;

    public string $search = '';
    public string $filter = 'all';
    public float $minRedflag = 0.33;

    public array $suggestions = [];
    public array $overrides = [];

    /**
     * Classifications description map:
     *  - bp:  [id => name]
     *  - dre: [id => name]
     */
    public array $classify = [];

    /**
     * Options used by selects (same structure as $classify).
     * Exists to keep naming explicit in the view.
     */
    public array $classifyOptions = [];

    /**
     * Auditor overrides for classification (only in preview, until applyPreview()):
     *  [rowId => ['bp' => int|null, 'dre' => int|null]]
     */
    public array $classifyOverrides = [];

    public int $minConfidence = 16;

    public string $sortField = 'file_line';
    public string $sortDirection = 'asc';

    public function mount(ImportFile $file, TrialBalanceAiPreviewStore $store, TrialBalanceAiSuggester $suggester): void
    {
        $this->file = $file;

        $userId = auth()->id();
        $cached = $store->get($file->id, $userId);
        $cached = false;

        if (!$cached) {
            $suggestions = $suggester->suggestForFile($file->id);

            $this->classify = $suggester->getClassificationDesc($file->id);
            $this->classifyOptions = $this->classify;

            $cached = [
                'meta' => [
                    'batch_id' => (string) Str::uuid(),
                    'model' => 'mvp-heuristic',
                    'created_at' => now()->toDateTimeString(),
                ],
                'suggestions' => $suggestions,
                'classify_overrides' => [],
                'included_overrides' => [],
            ];

            $store->put($file->id, $userId, $cached);
        } else {
            // Restore cached options (if exists)
            $this->classify = $suggester->getClassificationDesc($file->id);
            $this->classifyOptions = $this->classify;
        }

        $this->suggestions = $cached['suggestions'] ?? [];
        $this->overrides = $cached['included_overrides'] ?? [];
        $this->classifyOverrides = $cached['classify_overrides'] ?? [];
    }

    public function sortBy(string $field): void
    {
        $allowed = ['file_line', 'previous_balance', 'debit', 'credit', 'monthly_activity', 'closing_balance'];
        if (!in_array($field, $allowed, true)) return;

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    private function effectiveIncluded(int $rowId): ?bool
    {
        if (array_key_exists($rowId, $this->overrides)) {
            return (bool) $this->overrides[$rowId];
        }

        if (isset($this->suggestions[$rowId])) {
            return (bool) $this->suggestions[$rowId]['included'];
        }

        return null;
    }

    /**
     * Decide if this row should be classified as BP based on your current rule:
     * BP buckets are: 1.1., 1.2., 2.1., 2.2., 2.4.
     */
    private function isBpAccount(?string $account): bool
    {
        if (!is_string($account) || $account === '') return false;

        $four = substr($account, 0, 4);

        return in_array($four, ['1.1.', '1.2.', '2.1.', '2.2.', '2.4.'], true);
    }

    /**
     * Returns the effective classification ids for a row,
     * considering auditor overrides first, then suggested defaults.
     */
    private function effectiveClassification(int $rowId): array
    {
        if (isset($this->classifyOverrides[$rowId])) {

            $bp  = $this->classifyOverrides[$rowId]['bp']  ?? null;
            $dre = $this->classifyOverrides[$rowId]['dre'] ?? null;

            return [
                'balance_sheet_id'    => $bp ?: null,
                'income_statement_id' => $dre ?: null,
            ];
        }

        return [
            'balance_sheet_id'    => $this->suggestions[$rowId]['balance_sheet_id'] ?? null,
            'income_statement_id' => $this->suggestions[$rowId]['income_statement_id'] ?? null,
        ];
    }

    /**
     * Set classification override for a row in preview.
     * Only one side can be set at a time.
     */
    public function setClassification(int $rowId, string $type, $id): void
    {
        $id = ($id === '' || $id === null) ? null : (int) $id;

        $this->classifyOverrides[$rowId] ??= ['bp' => null, 'dre' => null];

        if ($type === 'bp') {
            $this->classifyOverrides[$rowId]['bp']  = $id;
            $this->classifyOverrides[$rowId]['dre'] = null;
        }

        if ($type === 'dre') {
            $this->classifyOverrides[$rowId]['dre'] = $id;
            $this->classifyOverrides[$rowId]['bp']  = null;
        }
    }

    public function toggleIncluded(int $rowId, bool $value): void
    {
        $this->overrides[$rowId] = $value;
    }

    public function clearOverrides(): void
    {
        $this->overrides = [];
        $this->classifyOverrides = [];
        $this->dispatch('toast', message: __('labels.changes_discarded'));
    }

    public function applyPreview(): void
    {
        $batchId = (string) Str::uuid();

        $rows = TrialBalanceData::query()
            ->where('file_id', $this->file->id)
            ->select(['id', 'file_id', 'company_id'])
            ->get();

        DB::transaction(function () use ($rows, $batchId) {
            foreach ($rows as $row) {
                $finalIncluded = $this->effectiveIncluded($row->id);

                if ($finalIncluded === null) {
                    continue;
                }

                $suggested = $this->suggestions[$row->id]['included'] ?? null;
                $source = ($suggested === null)
                    ? 'manual'
                    : ((bool) $suggested === (bool) $finalIncluded ? 'ai_approved' : 'ai_modified');

                $rationale  = $this->suggestions[$row->id]['rationale'] ?? null;
                $confidence = $this->suggestions[$row->id]['confidence'] ?? null;

                $cls = $this->effectiveClassification($row->id);
                $balanceSheetId     = $cls['balance_sheet_id'] ?? null;
                $incomeStatementId  = $cls['income_statement_id'] ?? null;

                $balanceSheetId    = ($balanceSheetId === '' ? null : $balanceSheetId);
                $incomeStatementId = ($incomeStatementId === '' ? null : $incomeStatementId);

                $balanceSheetId    = is_numeric($balanceSheetId) ? (int) $balanceSheetId : $balanceSheetId;
                $incomeStatementId = is_numeric($incomeStatementId) ? (int) $incomeStatementId : $incomeStatementId;

                $decision = TrialBalanceDecision::create([
                    'trial_balance_data_id' => $row->id,
                    'file_id'               => $row->file_id,
                    'company_id'            => $row->company_id,
                    'balance_included'      => (bool) $finalIncluded,
                    'source'                => $source,
                    'reason'                => $source === 'ai_approved'
                        ? __('labels.automatic_suggestion_approved')
                        : __('labels.adjustement_made_auditor'),
                    'ai_confidence'         => $confidence,
                    'ai_model'              => 'ai-preview',
                    'ai_rationale'          => $rationale,
                    'batch_id'              => $batchId,
                    'decided_user_id'       => auth()->id(),
                    'decided_at'            => now(),

                    'balance_sheet_id'      => $balanceSheetId,
                    'income_statement_id'   => $incomeStatementId,
                ]);

                $rowFull = TrialBalanceData::find($row->id);

                $rowFull->forceFill([
                    'balance_included'         => (bool) $finalIncluded,
                    'balance_last_decision_id' => $decision->id,
                    'balance_decision_source'  => $source,
                    'decided_user_id'          => auth()->id(),
                    'balance_decided_at'       => $decision->decided_at,
                    'balance_sheet_id'         => $balanceSheetId,
                    'income_statement_id'      => $incomeStatementId,
                ])->save();
            }
        });

        $hasIncluded = TrialBalanceData::query()
            ->where('file_id', $this->file->id)
            ->where('balance_included', true)
            ->exists();

        $this->file->forceFill(['file_status_id' => $hasIncluded ? 3 : 2])->save();

        app(TrialBalanceAiPreviewStore::class)->forget($this->file->id, auth()->id());

        $this->dispatch('toast', message: __('labels.decisions_applied'));
    }

    public function render()
    {
        $query = TrialBalanceData::query()
            ->where('file_id', $this->file->id)
            ->when($this->search !== '', function ($q) {
                $term = trim($this->search);
                $q->where(function ($w) use ($term) {
                    $w->where('account', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('file_line', 'asc');

        $rows = $query->get();

        $totalFileClosing = (float) TrialBalanceData::query()
            ->where('file_id', $this->file->id)
            ->sum('closing_balance');

        $previewSum = 0.0;
        foreach ($rows as $r) {
            if ($this->effectiveIncluded($r->id) === true) {
                $previewSum += (float) $r->closing_balance;
            }
        }

        $filtered = $rows->filter(function ($r) {
            $inc = $this->effectiveIncluded($r->id);

            return match ($this->filter) {
                'included' => $inc === true,
                'excluded' => $inc === false,
                'changed'  => array_key_exists($r->id, $this->overrides)
                    && isset($this->suggestions[$r->id])
                    && (bool) $this->overrides[$r->id] !== (bool) $this->suggestions[$r->id]['included'],
                'low_confidence' => isset($this->suggestions[$r->id]['confidence'])
                    && (int) $this->suggestions[$r->id]['confidence'] < $this->minConfidence,
                'redflag' => isset($this->suggestions[$r->id]['redflag'])
                    && (float) $this->suggestions[$r->id]['redflag'] >= $this->minRedflag,
                default => true,
            };
        });

        return view('livewire.tools.balance.validate-trial-balance-ai-preview', [
            'rows' => $filtered,
            'totalFileClosing' => $totalFileClosing,
            'previewSum' => $previewSum,
            'diff' => $previewSum - $totalFileClosing,
        ])->layout('layouts.app');
    }
}
