<?php

namespace App\Livewire\Register\BalanceSheet;

use App\Models\BalanceSheet;
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
        $term     = trim($this->search);
        $response = BalanceSheet::query()
            ->when($term !== '', function ($q) use ($term){
                $q->where(function($qq) use ($term){
                    $qq->where('name', 'like', "%{$term}%")
                        ->orWhere('code', 'like', "%{$term}%")
                        ->orWhere('side', 'like', "%{$term}%")
                        ->orWhere('section', 'like', "%{$term}%");
                });
            })
            ->orderBy('sort_order')
            ->paginate(10);

        return view('livewire.register.balance-sheet.index',
            compact('response')
        )->layout('layouts.app');
    }
}
