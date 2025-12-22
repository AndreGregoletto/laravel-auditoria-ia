<?php

namespace App\Livewire\CompanyTree;

use App\Models\CompanyTree;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $aCompanyTree = CompanyTree::select('id', 'company_parent_id', 'levels', 'status')
            ->with([
                'company' => function($q){
                    $q->select('id', 'name', 'status')
                        ->where('status', 1);
                }
            ])
            ->where('status', 1)
            ->where('levels', 1)
            ->get();

        $aCompanyTree = $aCompanyTree->map(function ($aCompanyTree){
            if(!$aCompanyTree->company){
//                exit;
            }

            return [
                'company_parent_id' => $aCompanyTree->company_parent_id,
                'company_name'      => $aCompanyTree->company->name
            ];
        });

        dd($aCompanyTree);
        return view('livewire.company-tree.index')->layout('layouts.app');
    }
}
