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

            $cached = [
                'meta' => [
                    'batch_id' => (string) Str::uuid(),
                    'model' => 'mvp-heuristic',
                    'created_at' => now()->toDateTimeString(),
                ],
                'suggestions' => $suggestions,
            ];

            $store->put($file->id, $userId, $cached);
        }

        $this->suggestions = $cached['suggestions'] ?? [];
        $this->overrides = [];
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

    public function toggleIncluded(int $rowId, bool $value): void
    {
        $this->overrides[$rowId] = $value;
    }

    public function clearOverrides(): void
    {
        $this->overrides = [];
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
                    : ((bool)$suggested === (bool)$finalIncluded ? 'ai_approved' : 'ai_modified');

                $rationale = $this->suggestions[$row->id]['rationale'] ?? null;
                $confidence = $this->suggestions[$row->id]['confidence'] ?? null;

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
                ]);

                // snapshot no dado
                $rowFull = TrialBalanceData::find($row->id);
                $rowFull->forceFill([
                    'balance_included'         => (bool) $finalIncluded,
                    'balance_last_decision_id' => $decision->id,
                    'balance_decision_source'  => $source,
                    'decided_user_id'          => auth()->id(),
                    'balance_decided_at'       => $decision->decided_at,
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
        // return redirect()->route('processes.validate.edit', $this->file->id);
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

        // filtro por included/excluded/changed/low_confidence em memória (após carregar)
        $filtered = $rows->filter(function ($r) {
            $inc = $this->effectiveIncluded($r->id);

            return match ($this->filter) {
                'included' => $inc === true,
                'excluded' => $inc === false,
                'changed'  => array_key_exists($r->id, $this->overrides)
                    && isset($this->suggestions[$r->id])
                    && (bool)$this->overrides[$r->id] !== (bool)$this->suggestions[$r->id]['included'],
                'low_confidence' => isset($this->suggestions[$r->id]['confidence'])
                    && (int)$this->suggestions[$r->id]['confidence'] < $this->minConfidence,
                'redflag' => isset($this->suggestions[$r->id]['redflag'])
                    && (float)$this->suggestions[$r->id]['redflag'] >= $this->minRedflag,
                default => true,
            };
        });
        dd($this->suggestions);
        dd($filtered[0]);
        return view('livewire.tools.balance.validate-trial-balance-ai-preview', [
            'rows' => $filtered,
            'totalFileClosing' => $totalFileClosing,
            'previewSum' => $previewSum,
            'diff' => $previewSum - $totalFileClosing,
        ])->layout('layouts.app');
    }
}
