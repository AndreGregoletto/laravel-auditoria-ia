<?php

namespace App\Livewire\Register\BalanceSheet;

use App\Models\BalanceSheet;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public array $form = [];
    public array $group = [];

    public function mount(): void
    {
        $this->form = [
            'code'            => '',
            'name'            => '',
            'company_tree_id' => null,   // por enquanto desativado
            'company_id'      => null,   // por enquanto desativado
            'parent_code'     => null,
            'sort_order'      => null,
            'side'            => null,
            'section'         => null,
            'status'          => true,
        ];

        // grupo = lista de possíveis parent_code (global por enquanto)
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
                Rule::unique('balance_sheets', 'code'),
            ],
            'form.name' => ['required', 'string', 'max:255'],

            // Por enquanto ficam null e desabilitados; ainda assim validamos formato.
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
        $this->validate();

        // Regra de prefixo: se tem parent_code, code precisa começar com "PARENT."
        $parent = $this->form['parent_code'] ?? null;
        $code   = $this->form['code'] ?? null;

        if (!empty($parent) && !empty($code)) {
            $expectedPrefix = $parent . '.';

            if (stripos($code, $expectedPrefix) !== 0) {
                $this->addError('form.code', "O código deve começar com \"{$expectedPrefix}\" pois pertence ao grupo \"{$parent}\".");
                return;
            }
        }

        // normaliza strings vazias -> null
        if (empty($this->form['parent_code'])) {
            $this->form['parent_code'] = null;
        }
        if (empty($this->form['company_tree_id'])) {
            $this->form['company_tree_id'] = null;
        }
        if (empty($this->form['company_id'])) {
            $this->form['company_id'] = null;
        }

        BalanceSheet::create($this->form);

        return redirect()->route('settings.register.asset-base-classification.index');
    }

    public function render()
    {
        return view('livewire.register.balance-sheet.create', [
            'group' => $this->group,
        ])->layout('layouts.app');
    }
}
