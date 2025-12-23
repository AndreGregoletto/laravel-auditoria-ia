<?php

namespace App\Livewire\CompanyTree;

use App\Models\Company;
use App\Models\CompanyTree;
use Livewire\Component;
use Illuminate\Validation\ValidationException;


class Create extends Component
{
    public array $form = [
        'company_parent_id' => '',
        'status' => true,
    ];

    public function  rules(): array
    {
        return [
            'form.company_parent_id' => ['required', 'integer'],
            'form.status' => ['boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'form.company_parent_id.required' => __('error.company_required'),
//            'form.unique'        => __('error.name_unique'),
        ];
    }

    public function save()
    {
        $this->validate(
            $this->rules(),
            $this->messages()
        );

        $idCompany = $this->form['company_parent_id'];

        if(
            CompanyTree::where('company_parent_id', $idCompany)
                ->where('status', 1)
                ->where('levels', 1)
                ->first()
        ){
            throw ValidationException::withMessages([
                'form.company_parent_id' => trans(__('error.company_has_active'))
            ]);
        }

        $aCompanyTree = [
            'company_parent_id' => $idCompany,
            'company_id'        => $idCompany,
            'company_parent'    => 1,
            'levels'            => 1,
            'status'            => 1,
        ];
//        dd($aCompanyTree);
        CompanyTree::create($aCompanyTree);

        return redirect()->route('companies_tree.index');
    }

    public function render()
    {
        return view('livewire.company-tree.create', [
            'companies' => Company::orderBy('name')->get()
        ])->layout('layouts.app');
    }
}
