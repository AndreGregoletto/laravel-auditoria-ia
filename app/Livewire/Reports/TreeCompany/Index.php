<?php

namespace App\Livewire\Reports\TreeCompany;

use App\Models\CompanyTree;
use App\Services\CompanyTreeOrderService;
use Livewire\Component;

class Index extends Component
{
    public int $company_tree;
    public $companies;


    public function mount(int $company_tree) :void
    {
        $this->company_tree = $company_tree;
        $this->loadCompanies();
    }

    public function loadCompanies(): void
    {
        $nodes = CompanyTree::query()
            ->where('company_tree_id', $this->company_tree)
            ->with(['company', 'companyParent:id,name'])
            ->get();

        $ordered = CompanyTreeOrderService::orderPreOrder($nodes);
        $this->companies = $ordered;
    }

    public function render()
    {
        return view('livewire.reports.tree-company.index', [
            'companies' => $this->companies
        ])->layout('layouts.app');
    }
}
