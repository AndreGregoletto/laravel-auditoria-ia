<?php

namespace App\Livewire\Register\BalanceSheet\Relater;

use App\Models\PivotBalanceSheetReference;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $term = trim($this->search);
        $response = PivotBalanceSheetReference::query()
            ->with(['balanceSheet', 'companyTree', 'company', 'userCreate', 'userAlter'])
//            ->orderBy(['company_tree_id', 'company_id'])
            ->get();
//            ->paginate(10);
        dd($response[0]);
        return view('livewire.register.balance-sheet.relater.index')->layout('layouts.app');
    }
}
