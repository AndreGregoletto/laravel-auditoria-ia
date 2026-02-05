<?php

namespace App\Livewire\Register\IncomeStatement;

use App\Models\IncomeStatement;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public IncomeStatement $incomeStatement;

    public array $form  = [];
    public array $group = [];

    public function mount(IncomeStatement $incomeStatement): void
    {
        $this->incomeStatement = $incomeStatement;

        $this->form = $incomeStatement->only([
            'code', 'name', 'company_tree_id',
            'company_id', 'status', 'parent_code',
            'sort_order', 'config_name',
        ]);

        $this->form['status'] = (bool) ($this->form['status'] ?? true);
        $this->group = IncomeStatement::query()
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
                Rule::unique('income_statements', 'code')->ignore($this->incomeStatement->id),
            ],
            'form.name' => ['required', 'string', 'max:255'],

            'form.company_tree_id' => ['nullable', 'integer'],
            'form.company_id'      => ['nullable', 'integer'],

            'form.parent_code' => [
                'nullable',
                'string',
                'max:10',
                Rule::exists('income_statements', 'code'),
            ],

            'form.sort_order' => ['nullable', 'integer', 'min:1', 'max:999999'],

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

        $this->incomeStatement->update($this->form);

        return redirect()->route('settings.register.income-statement-classification.index');
    }

    public function render()
    {
        return view('livewire.register.income-statement.edit', [
            'group' => $this->group,
        ])->layout('layouts.app');
    }
}
