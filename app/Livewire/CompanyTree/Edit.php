<?php

namespace App\Livewire\CompanyTree;

use App\Models\Company;
use App\Models\CompanyTree;
use Livewire\Component;

class Edit extends Component
{
    public int $company_tree;
    public $companies;

    public bool $showAddChildModal = false;
    public ?int $parentTreeId = null;
    public ?int $childCompanyId = null;
    public $availableCompanies = [];

    public bool $showToggleStatusModal = false;
    public ?int $toggleTreeId = null;
    public bool $nextStatus = false;

    public function mount($company_tree): void
    {
        $this->company_tree = $company_tree;
        $this->loadCompanies();
    }

    private function loadCompanies(): void
    {
        $query = CompanyTree::where('company_tree_id', $this->company_tree)
            ->with(['company'])
            ->orderBy('levels', 'asc');

        $response = [];
        $children = [];

        foreach ($query->get() as $company){
//            dd($company->company_tree_id);
            $response[$company->company_parent_id][$company->company_id] = [];
//            $response[''] = [
//                "id"              => $company->id,
//                "company_tree_id" => $company->company_tree_id,
//                "company_id"      => $company->company_id,
//                "holding"         => $company->holding,
//                "levels"          => $company->levels,
//                "status"          => $company->status,
//                "company" => [
//                    "name"            => $company->company->name,
//                    "commercial_name" => $company->company->commercial_name,
//                    "cnpj"            => $company->company->cnpj,
//                    "publicity_trade" => $company->company->publicity_trade,
//                    "status"          => $company->company->status,
//                ],
//            ];
//            dd($response);
        }
        dd($response);
    }

    public function openAddChild(int $treeId): void
    {
        $this->resetValidation();
        $this->parentTreeId = $treeId;
        $this->childCompanyId = null;

        $treeCompany = CompanyTree::select('company_id', 'status')
            ->where('status', 1)
            ->pluck('company_id');

        $this->availableCompanies = Company::query()
            ->where('status', 1)
            ->whereNotIn('id', $treeCompany)
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->showAddChildModal = true;
    }

    public function closeAddChild(): void
    {
        $this->showAddChildModal = false;
        $this->parentTreeId = null;
        $this->childCompanyId = null;
    }

    public function storeChild(): void
    {
        $this->validate([
            'parentTreeId'   => ['required', 'integer', 'exists:company_trees,id'],
            'childCompanyId' => ['required', 'integer', 'exists:companies,id'],
        ]);

        $parent = CompanyTree::query()
            ->where('company_id', $this->parentTreeId)
            ->where('company_tree_id', $this->company_tree)
            ->firstOrFail();

        $parent->holding = 1;
        $parent->save();

        CompanyTree::create([
            'company_parent_id' => $this->parentTreeId,
            'company_tree_id'   => $this->company_tree,
            'company_id'        => $this->childCompanyId,
            'holding'           => 0,
            'levels'            => (int) $parent->levels + 1,
            'status'            => 1,
        ]);

        $this->closeAddChild();
        $this->loadCompanies();

        session()->flash('success', 'Empresa adicionada com sucesso.');
    }

    public function confirmToggleStatus(int $treeId): void
    {
        $this->toggleTreeId = $treeId;

        $node = CompanyTree::query()->findOrFail($treeId);
        $this->nextStatus = !$node->status;

        $this->showToggleStatusModal = true;
    }

    public function closeToggleStatus(): void
    {
        $this->showToggleStatusModal = false;
        $this->toggleTreeId = null;
        $this->nextStatus = false;
    }

    public function toggleStatusConfirmed(): void
    {
        $node = CompanyTree::query()->findOrFail($this->toggleTreeId);
        $node->update(['status' => $this->nextStatus ? 1 : 0]);

        $this->closeToggleStatus();
        $this->loadCompanies();

        session()->flash('success', 'Status atualizado com sucesso.');
    }

    public function render()
    {
        return view('livewire.company-tree.edit', [
            'companies' => $this->companies
        ])->layout('layouts.app');
    }
}
