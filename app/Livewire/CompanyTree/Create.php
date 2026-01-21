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
            'form.company_tree_id' => ['required', 'integer'],
            'form.status' => ['boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'form.company_tree_id.required' => __('error.company_required'),
        ];
    }

    public function save()
    {
        $this->validate(
            $this->rules(),
            $this->messages()
        );

        $idCompany = $this->form['company_tree_id'];

        if(
            CompanyTree::where('company_tree_id', $idCompany)
                ->where('status', 1)
                ->where('levels', 1)
                ->first()
        ){
            throw ValidationException::withMessages([
                'form.company_tree_id' => trans(__('error.company_has_active'))
            ]);
        }

        $aCompanyTree = [
            'company_tree_id'   => $idCompany,
            'company_parent_id' => $idCompany,
            'company_id'        => $idCompany,
            'company_parent'    => 1,
            'holding'           => 1,
            'levels'            => 1,
            'status'            => 1,
        ];

        CompanyTree::create($aCompanyTree);

        return redirect()->route('companies_tree.index');
    }

    public function render()
    {
        return view('livewire.company-tree.create', [
            'companies' => Company::orderByRaw("COALESCE(commercial_name, name)")->get()
        ])->layout('layouts.app');
    }
}
