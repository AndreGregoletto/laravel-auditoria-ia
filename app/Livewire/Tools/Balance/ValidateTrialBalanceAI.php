<?php

namespace App\Livewire\Tools\Balance;

use App\Models\ImportFile;
use App\Models\TrialBalanceDecision;
use Livewire\Component;

class ValidateTrialBalanceAI extends Component
{
    public ImportFile $file;
    public bool $hasConfig = false;

    public function mount(ImportFile $file): void
    {
        $this->file = $file;
        dd($file);
    }

    public function render()
    {
        $teste = TrialBalanceDecision::query()
            ->distinct('file_id')
            ->get();

        dd($teste);
        return view('livewire.tools.balance.validate-trial-balance-a-i')->layout('layouts.app');
    }
}
