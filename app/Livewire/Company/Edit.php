<?php

namespace App\Livewire\Company;

use App\Models\Company;
use Livewire\Component;

class Edit extends Component
{
    public Company $company;

    public array $form = [];

    public function mount(Company $company): void
    {
        $this->company = $company;
        $this->form = $company->only(['name','commercial_name','cnpj','publicity_trade','status']);
    }

    protected function rules(): array
    {
        return [
            'form.name'            => ['required', 'string', 'max:255'],
            'form.commercial_name' => ['nullable', 'string', 'max:255'],
            'form.cnpj'            => ['required', 'string', 'max:20', 'unique:companies,cnpj,' . $this->company->id],
            'form.publicity_trade' => ['boolean'],
            'form.status'          => ['boolean'],
        ];
    }

    public function save()
    {
        $this->validate();
        $this->company->update($this->form);

        return redirect()->route('settings.companies.index');
    }

    public function render()
    {
        return view('livewire.company.edit')->layout('layouts.app');
    }
}
