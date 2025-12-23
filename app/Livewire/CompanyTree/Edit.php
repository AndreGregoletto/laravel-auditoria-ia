<?php

namespace App\Livewire\CompanyTree;

use App\Models\Company;
use App\Models\CompanyTree;
use Livewire\Component;

class Edit extends Component
{
    public int $company_tree;

    public function mount($company_tree): void
    {
        $this->company_tree = $company_tree;
    }
    public function render()
    {
        $query = CompanyTree::where('status', 1)
            ->where('levels', 1)
            ->where('company_parent_id', $this->company_tree)
            ->with(['company'])
            ->with(['company'])
            ->orderBy('levels', 'asc');

        $companies = $query->get() ?? null;

        return view('livewire.company-tree.edit', compact('companies'))->layout('layouts.app');
    }
}
