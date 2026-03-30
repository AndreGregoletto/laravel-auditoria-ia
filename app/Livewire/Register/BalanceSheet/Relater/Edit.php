<?php

namespace App\Livewire\Register\BalanceSheet\Relater;

use App\Models\BalanceSheet;
use App\Models\Company;
use App\Models\CompanyTree;
use App\Models\PivotBalanceSheetReference;
use Livewire\Component;

class Edit extends Component
{
    public PivotBalanceSheetReference $pivotBalanceSheetReference;

    public array $form = [];
    public array $group = [];

    public function mount(PivotBalanceSheetReference $pivotBalanceSheetReference): void
    {
        $this->pivotBalanceSheetReference = $pivotBalanceSheetReference;

        $companyId   = $pivotBalanceSheetReference->company_id;
        $companyTree = $pivotBalanceSheetReference->company_tree_id;

        $query = BalanceSheet::query();

        $this->group = $query
            ->orderByRaw('COALESCE(sort_order, 999999) ASC')
            ->get(['id', 'code', 'name'])
            ->mapWithKeys(fn ($item) => [
                $item->id => trim(($item->code ? $item->code . ' - ' : '') . $item->name)
            ])
            ->toArray();

        $companyTree = CompanyTree::with('company')->find($companyTree);
        $companyTree = $companyTree?->company;
        $company     = Company::find($companyId);

        $this->form = [
            'balance_sheet_id' => $pivotBalanceSheetReference->balance_sheet_id,
            'value'            => $pivotBalanceSheetReference->value,
            'company_tree_id'  => $companyTree === null ? '' : ($companyTree->comercial_name ?? $companyTree->name),
            'company_id'       => $company     === null ? '' : ($company->comercial_name ?? $company->name),
            'status'           => (bool) $pivotBalanceSheetReference->status,
        ];
    }

    public function save(): void
    {
        $data = $this->validate([
            'form.balance_sheet_id' => ['required', 'integer', 'exists:balance_sheets,id'],
            'form.value'            => ['nullable', 'string', 'max:255'],
            'form.status'           => ['boolean'],
        ])['form'];

        $this->pivotBalanceSheetReference->update([
            'balance_sheet_id' => $data['balance_sheet_id'],
            'value'            => $data['value'] ?? null,
            'status'           => (int) ($data['status'] ?? false),
        ]);

        session()->flash('success', __('success.save'));

        $this->redirectRoute(
            'settings.register.asset-base-classification.relator.index',
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.register.balance-sheet.relater.edit')
            ->layout('layouts.app');
    }
}