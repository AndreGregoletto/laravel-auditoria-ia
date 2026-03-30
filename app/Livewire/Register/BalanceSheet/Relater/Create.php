<?php

namespace App\Livewire\Register\BalanceSheet\Relater;

use App\Models\BalanceSheet;
use App\Models\Company;
use App\Models\CompanyTree;
use App\Models\PivotBalanceSheetReference;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public array $form = [];
    public array $group = [];
    public array $companies = [];
    public array $companyTrees = [];

    public function mount(): void
    {
        $this->companies = Company::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->id => $item->commercial_name ?? $item->name
            ])
            ->toArray();

        $this->companyTrees = CompanyTree::query()
            ->where('levels', 1)
            ->with('company')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function ($item) {
                $label = $item->company?->commercial_name
                    ?? $item->company?->name
                    ?? 'Tree #' . $item->id;

                return [$item->company?->id => $label];
            })
            ->toArray();
// dd(1);
        $this->form = [
            'balance_sheet_id' => null,
            'value'            => null,
            'company_tree_id'  => null,
            'company_id'       => null,
            'status'           => true,
        ];

        $this->loadGroup();
    }

    public function updatedFormCompanyId(): void
    {
        $this->loadGroup();
    }

    public function updatedFormCompanyTreeId(): void
    {
        if($this->form['company_tree_id'] !== ''){
            $companies = CompanyTree::query()
                ->where('company_tree_id', $this->form['company_tree_id'])
                ->with('company')            
                ->orderBy('name')
                ->get()
                ->mapWithKeys(function ($item) {
                    $label = $item->company?->commercial_name
                        ?? $item->company?->name
                        ?? 'Tree #' . $item->id;

                    return [$item->id => $label];
                })
                ->toArray();
            $this->companies = $companies;
        }else{
            $this->companies = Company::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->id => $item->commercial_name ?? $item->name
            ])
            ->toArray();
        }
        $this->loadGroup();
    }

    private function loadGroup(): void
    {
        $this->group = BalanceSheet::query()
            ->orderByRaw('COALESCE(sort_order, 999999) ASC')
            ->get(['id', 'code', 'name'])
            ->mapWithKeys(fn ($item) => [
                $item->id => trim(($item->code ? $item->code . ' - ' : '') . $item->name)
            ])
            ->toArray();
    }

    public function save(): void
    {
        $data = $this->validate([
            'form.balance_sheet_id' => ['required', 'integer', 'exists:balance_sheets,id'],
            'form.value'            => ['nullable', 'string', 'max:255'],
            'form.company_tree_id'  => ['nullable', 'integer', 'exists:company_trees,id'],
            'form.company_id'       => ['nullable', 'integer', 'exists:companies,id'],
            'form.status'           => ['boolean'],
        ])['form'];

        PivotBalanceSheetReference::create([
            'balance_sheet_id' => $data['balance_sheet_id'],
            'value'            => $data['value'] ?? null,
            'company_tree_id'  => $data['company_tree_id'] ?: null,
            'company_id'       => $data['company_id'] ?: null,
            'status'           => (int) ($data['status'] ?? false),
            'create_user_id'   => Auth::id(),
            'alter_user_id'    => Auth::id(),
        ]);

        session()->flash('success', __('success.save'));

        $this->redirectRoute(
            'settings.register.asset-base-classification.relator.index',
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.register.balance-sheet.relater.create')
            ->layout('layouts.app');
    }
}