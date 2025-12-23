<?php

namespace App\Livewire\CompanyTree;

use App\Models\CompanyTree;
use Livewire\Component;
use function PHPUnit\Framework\isBool;

class Index extends Component
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
            ->select('id', 'company_parent_id', 'levels', 'status')
            ->where('status', 1)
            ->where('levels', 1)
            ->with(['company:id,name,status']);

        if ($term !== '') {
            if(in_array($term, [$true, $false])){
                $boll = $term === $true ? 1 : 0;
                $query->where('status', $boll);
            }else{
                $query->whereHas('company', function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%");
                });
            }
        }

        $aCompanyTree = $query->get()
            ->map(fn ($row) => [
                'id'     => $row->company_parent_id,
                'name'   => $row->company?->name ?? '',
                'status' => (bool) $row->status,
            ])
            ->filter(fn ($row) => $row['name'] !== '')
            ->values();

        return view('livewire.company-tree.index', compact('aCompanyTree'))
            ->layout('layouts.app');
    }

    public function toUp($string) : string
    {
        return strtoupper($string);
    }
}
