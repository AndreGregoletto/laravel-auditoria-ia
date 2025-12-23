<?php

namespace App\Livewire\CompanyTree;

use App\Models\Company;
use App\Models\CompanyTree;
use Livewire\Component;

class Edit extends Component
{
    public function render()
    {
        $query = CompanyTree::where('status', 1)
            ->where('levels', 1)
            ->with(['company']);

        return view('livewire.company-tree.edit')->layout('layouts.app');
    }
}
