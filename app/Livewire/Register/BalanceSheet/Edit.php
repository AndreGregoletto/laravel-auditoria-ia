<?php

namespace App\Livewire\Register\BalanceSheet;

use App\Models\BalanceSheet;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public BalanceSheet $balanceSheet;

    public array $form = [];
    public array $group = [];

    public function mount(BalanceSheet $balanceSheet): void
    {
        $this->balanceSheet = $balanceSheet;

        $this->form = $balanceSheet->only([
            'code', 'name',
            'company_tree_id', 'company_id',
            'status', 'parent_code',
            'sort_order', 'side', 'section',
        ]);

        $this->form['status'] = (bool) ($this->form['status'] ?? true);

        $this->group = BalanceSheet::query()
            ->whereNull('company_tree_id')
            ->whereNull('company_id')
            ->orderByRaw('COALESCE(sort_order, 999999) ASC')
            ->pluck('code', 'code')
            ->toArray();
    }

    protected function rules(): array
    {
        return [
            'form.code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('balance_sheets', 'code')->ignore($this->balanceSheet->id),
            ],
            'form.name' => ['required', 'string', 'max:255'],

            'form.company_tree_id' => ['nullable', 'integer'],
            'form.company_id'      => ['nullable', 'integer'],

            'form.parent_code' => [
                'nullable',
                'string',
                'max:10',
                Rule::exists('balance_sheets', 'code'),
            ],

            'form.sort_order' => ['nullable', 'integer', 'min:1', 'max:999999'],

            'form.side' => ['required', Rule::in(['assets', 'liabilities', 'equity'])],
            'form.section' => ['required', Rule::in(['current', 'non_current', 'equity'])],

            'form.status' => ['boolean'],
        ];
    }

    public function save()
    {
        $data = $this->validate();

        $parent = $this->form['parent_code'] ?? null;
        $code   = $this->form['code'] ?? null;

        if (!empty($parent) && !empty($code)) {
            $expectedPrefix = $parent . '.';

            if (stripos($code, $expectedPrefix) !== 0) {
                $this->addError('form.code', "O código deve começar com \"{$expectedPrefix}\" pois pertence ao grupo \"{$parent}\".");
                return;
            }
        }

        if (empty($this->form['parent_code'])) {
            $this->form['parent_code'] = null;
        }

        $this->balanceSheet->update($this->form);

        return redirect()->route('settings.register.asset-base-classification.index');
    }

    public function render()
    {
        return view('livewire.register.balance-sheet.edit', [
            'group' => $this->group,
        ])->layout('layouts.app');
    }
}
