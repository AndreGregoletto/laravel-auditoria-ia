<?php

namespace App\Livewire\Register\IncomeStatement;

use App\Models\IncomeStatement;
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
        $response = IncomeStatement::query()
            ->when($term !== '', function($q) use ($term){
                $q->where(function($qq) use ($term){
                    $qq->where('name', 'like', "%{$term}%")
                        ->orWhere('code', 'like', "%{$term}%")
                        ->orWhere('parent_code', 'like', "%{$term}%")
                        ->orWhere('config_name', 'like', "%{$term}%");
                });
            })
        ->orderBy('sort_order')
        ->paginate(10);

        return view('livewire.register.income-statement.index',
            compact('response')
        )->layout('layouts.app');
    }
}
