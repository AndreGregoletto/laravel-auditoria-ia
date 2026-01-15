<?php

namespace App\Livewire\Tools\Balance;

use App\Models\ImportFile;
use App\Models\TrialBalanceDecision;
use App\Models\Queue\TrialBalanceData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class ValidateTrialBalanceEdit extends Component
{
    public ImportFile $file;

    public string $search = '';
    public string $filterIncluded = 'all';

    // modal decisão
    public ?int $editingRowId = null;
    public ?bool $editingValue = null;
    public string $reason = '';

    // bulk
    public ?int $bulkLength = null;
    public string $bulkAction = 'include'; // include|exclude
    public string $bulkReason = '';

    public function mount(ImportFile $file): void
    {
        $this->file = $file;
    }

    public function render()
    {
        $rows = TrialBalanceData::query()
            ->where('file_id', $this->file->id)
            ->when($this->search !== '', function ($q) {
                $term = trim($this->search);
                $q->where(function ($w) use ($term) {
                    $w->where('account', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->when($this->filterIncluded !== 'all', function ($q) {
                return match ($this->filterIncluded) {
                    'included'   => $q->where('balance_included', true),
                    'excluded'   => $q->where('balance_included', false),
                    'undecided'  => $q->whereNull('balance_included'),
                    'redflag'    => $q->where('red_flag', true),
                    default      => $q,
                };
            })
            ->orderBy('file_line')
            ->get();

        $totalClosingBalance = $rows->sum(function ($r) {
            return (float) $r->closing_balance;
        });

        return view('livewire.tools.balance.validate-trial-balance-edit', [
            'rows'  => $rows,
            'totalClosingBalance' => $totalClosingBalance
        ])->layout('layouts.app');
    }

    public function openDecision(int $rowId, bool $newValue): void
    {
        $this->editingRowId = $rowId;
        $this->editingValue = $newValue;
        $this->reason = '';
        $this->dispatch('open-modal', id: 'decision-modal');
    }

    public function saveDecision(): void
    {
        if (!$this->editingRowId || $this->editingValue === null) {
            return;
        }

        if (blank($this->reason)) {
            $this->addError('reason', 'Justificativa é obrigatória.');
            return;
        }

        $row = TrialBalanceData::where('file_id', $this->file->id)
            ->findOrFail($this->editingRowId);

        DB::transaction(function () use ($row) {
            $decision = TrialBalanceDecision::create([
                'trial_balance_data_id' => $row->id,
                'file_id'               => $row->file_id,
                'company_id'            => $row->company_id,
                'balance_included'      => (bool) $this->editingValue,
                'source'                => 'manual',
                'reason'                => $this->reason,
                'decided_user_id'       => auth()->id(),
                'decided_at'            => now(),
            ]);

            $row->forceFill([
                'balance_included'         => (bool) $this->editingValue,
                'balance_last_decision_id' => $decision->id,
                'balance_decision_source'  => 'manual',
                'decided_user_id'          => auth()->id(),
                'balance_decided_at'       => $decision->decided_at,
            ])->save();
        });

        $this->dispatch('close-modal', id: 'decision-modal');
        $this->reset(['editingRowId', 'editingValue', 'reason']);
        $this->dispatch('toast', message: 'Decisão registrada.');
    }

    public function applyBulkLength(): void
    {
        if (!$this->bulkLength || $this->bulkLength <= 0) {
            $this->addError('bulkLength', 'Informe um tamanho válido.');
            return;
        }
        if (blank($this->bulkReason)) {
            $this->addError('bulkReason', 'Justificativa é obrigatória para ação em massa.');
            return;
        }

        $batchId = (string) Str::uuid();
        $include = $this->bulkAction === 'include';

        $rows = TrialBalanceData::query()
            ->where('file_id', $this->file->id)
            ->get()
            ->filter(fn($r) => mb_strlen((string) $r->account) === (int) $this->bulkLength);

        DB::transaction(function () use ($rows, $include, $batchId) {
            foreach ($rows as $row) {
                $decision = TrialBalanceDecision::create([
                    'trial_balance_data_id' => $row->id,
                    'file_id'               => $row->file_id,
                    'company_id'            => $row->company_id,
                    'balance_included'      => $include,
                    'source'                => 'bulk_action',
                    'reason'                => $this->bulkReason,
                    'batch_id'              => $batchId,
                    'decided_user_id'       => auth()->id(),
                    'decided_at'            => now(),
                ]);

                $row->forceFill([
                    'balance_included'         => $include,
                    'balance_last_decision_id' => $decision->id,
                    'balance_decision_source'  => 'bulk_action',
                    'decided_user_id'          => auth()->id(),
                    'balance_decided_at'       => $decision->decided_at,
                ])->save();
            }
        });

        $this->dispatch('toast', message: "Ação em massa aplicada (batch {$batchId}).");
    }
}
