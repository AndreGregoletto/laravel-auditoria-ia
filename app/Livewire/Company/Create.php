<?php

namespace App\Livewire\Company;

use App\Models\Company;
use Livewire\Component;

class Create extends Component
{
    public array $form = [
        'name'            => '',
        'commercial_name' => '',
        'cnpj'            => '',
        'publicity_trade' => false,
        'status'          => true,
    ];

    protected function rules(): array
    {
        return [
            'form.name'            => ['required', 'string', 'max:255', 'unique:companies,name'],
            'form.commercial_name' => ['nullable', 'string', 'max:255', 'unique:companies,commercial_name'],
            'form.cnpj'            => ['required', 'string', 'max:20', 'unique:companies,cnpj'],
            'form.publicity_trade' => ['boolean'],
            'form.status'          => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'form.name.required'            => __('error.name_required'),
            'form.name.string'              => __('error.name_string'),
            'form.name.max'                 => __('error.name_max_255'),
            'form.name.unique'              => __('error.name_unique'),
            'form.commercial_name.required' => __('error.name_required'),
            'form.commercial_name.string'   => __('error.name_string'),
            'form.commercial_name.max'      => __('error.name_max_255'),
            'form.commercial_name.unique'   => __('error.name_unique'),
            'form.cnpj.required'            => __('error.name_required'),
            'form.cnpj.string'              => __('error.name_string'),
            'form.cnpj.max'                 => __('error.name_max_20'),
            'form.cnpj.unique'              => __('error.name_unique'),
            'form.publicity_trade.boolean'  => __('error.bool'),
            'form.status.boolean'           => __('error.bool'),
        ];
    }

    public function save()
    {
        $this->validate(
            $this->rules(),
            $this->messages()
        );

        Company::create($this->form);

        return redirect()->route('companies.index');
    }

    public function render()
    {
        return view('livewire.company.create')->layout('layouts.app');
    }
}
