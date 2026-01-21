<?php

namespace App\Livewire\Company;

use App\Models\Company;
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

        $companies = Company::query()
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', "%{$term}%")
                        ->orWhere('commercial_name', 'like', "%{$term}%")
                        ->orWhere('cnpj', 'like', "%{$term}%");
                });
            })
            ->orderByRaw("COALESCE(commercial_name, name)")
            ->paginate(10);

        return view('livewire.company.index', compact('companies'))
            ->layout('layouts.app');
    }
}
