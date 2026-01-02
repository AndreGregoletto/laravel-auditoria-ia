<?php

namespace App\Livewire\Reports;

use App\Models\Company;
use App\Models\CompanyTree;
use Livewire\Component;
use Livewire\WithPagination;

class TreeCompany extends Component
{
    public string $search = '';

    public function render()
    {
        $term = $this->search
                |> (fn($t) => trim($t))
                |> (fn($t) => $this->toUp($t));

        $true  = $this->toUp(__('reports.active'));
        $false = $this->toUp( __('reports.inactive'));

        $query = CompanyTree::query()
            ->where('levels', 1)
            ->with(['company']);

        if ($term !== '') {
            if(in_array($term, [$true, $false])){
                $boll = $term === $true ? 1 : 0;
                $query->where('status', $boll);
            }else{
                $query->whereHas('company', function ($q) use ($term) {
                    $q->where('commercial_name', 'like', "%{$term}%");
                });
            }
        }

        $aCompanyTree = $query->get()
            ->map(fn ($row) => [
                'id'              => $row->company_tree_id,
                'name'            => $row->companyTree?->name ?? '',
                'cnpj'            => $row->company?->cnpj ?? '',
                'commercial_name' => $row->companyTree?->commercial_name ?? '',
                'status'          => (bool) $row->status,
            ])
            ->filter(fn ($row) => $row['name'] !== '')
            ->values();

        return view('livewire.reports.tree-company', compact('aCompanyTree'))
            ->layout('layouts.app');
    }

    public function toUp($string) : string
    {
        return strtoupper($string);
    }
}
