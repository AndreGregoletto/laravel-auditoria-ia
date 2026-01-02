<?php

namespace App\Livewire\Reports;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class Companies extends Component
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
            ->where('status', 1)
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.reports.companies', compact('companies'))
            ->layout('layouts.app');
    }
}
