<?php

namespace App\Livewire\Register\IncomeStatement;

use App\Models\IncomeStatement;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public array $form  = [];
    public array $group = [];

    public function mount(): void
    {
        $this->form = [
            'code'            => '',
            'name'            => '',
            'company_tree_id' => null,
            'company_id'      => null,
            'status'          => true,
            'parent_code'     => null,
            'sort_order'      => null,
            'config_name'     => null,
        ];

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
                Rule::unique('balance_sheets', 'code'),
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
            'form.status' => ['boolean'],
        ];
    }

    public function save()
    {
        $this->validate();

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
        if (empty($this->form['company_tree_id'])) {
            $this->form['company_tree_id'] = null;
        }
        if (empty($this->form['company_id'])) {
            $this->form['company_id'] = null;
        }

        IncomeStatement::create($this->form);
        $this->dispatch('toast', message: 'Classificação criada');

        return redirect()->route('settings.register.income-statement-classification.index');

    }

    public function render()
    {
        return view('livewire.register.income-statement.create', [
            'group' => $this->group,
        ])->layout('layouts.app');
    }
}
