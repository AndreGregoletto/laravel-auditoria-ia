<?php

namespace App\Livewire\Register\BalanceSheet\Relater;

use App\Models\Company;
use App\Models\CompanyTree;
use App\Models\PivotBalanceSheetReference;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public $idCompany     = 0;
    public $idCompanyTree = 0;

    public Collection $companyTree;
    public Collection $company;
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $companyTree = CompanyTree::query()
            ->where('status', 1)
            ->where('levels', 1)
            ->with(['company'])
            ->get();

        $this->companyTree = $companyTree->map(function ($item) {
            return [
                'id'   => $item->company->id,
                'name' => $item->company->name,
            ];
        });

    }

    public function filterCompanies(): void
    {
        $this->validateIds();
        if($this->idCompanyTree){
            $company = CompanyTree::query()
                ->where('company_tree_id', $this->idCompanyTree)
                ->with(['company'])
                ->get();

            $companies = $company->map(function ($item) {
                return [
                    'id'   => $item->company->id,
                    'name' => $item->company->name,
                    'commercial_name' => $item->company->commercial_name,
                ];
            });
        }else{
            $companies = Company::query()
                ->select(['id', 'name', 'commercial_name'])
                ->where('status', 1)
                ->orderByRaw("COALESCE(commercial_name, name)")
                ->get();
        }

        $this->company = $companies;
    }

    public function validateIds(): void
    {
        $companyTree = $this->idCompanyTree > 0 ? $this->idCompanyTree : null;
        $this->idCompanyTree = $companyTree;

        $companies = $this->idCompany > 0 ? $this->idCompany : null;
        $this->idCompany = $companies;
    }

    public function render()
    {
        $this->filterCompanies();
        $this->validateIds();

        $term = trim($this->search);

        $response = PivotBalanceSheetReference::query()
            ->where('company_tree_id', $this->idCompanyTree)
            ->where('company_id', $this->idCompany)
            ->with(['balanceSheet', 'companyTree', 'company', 'userCreate', 'userAlter'])
            ->paginate(100);

        return view('livewire.register.balance-sheet.relater.index',
            compact('response')
        )->layout('layouts.app');
    }
}
