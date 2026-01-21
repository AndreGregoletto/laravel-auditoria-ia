<?php

namespace App\Livewire;

use App\Models\Company;
use App\Models\CompanyTree;
use App\Models\ImportFile;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public array $today = [];

    public function mount(): void
    {
        $start = Carbon::today();
        $imports = ImportFile::query()
            ->where('created_at', '>=', $start);

        $companies     = Company::query();
        $treeCompanies = CompanyTree::query();

        $this->today = [
            'all_file_import' => (clone $imports)->count(),

            'file_import_success' => (clone $imports)
                ->where('file_status_id', '>', 1)
                ->count(),

            'file_import_error' => (clone $imports)
                ->where('file_status_id', 1)
                ->count(),

            'file_import_balance' => (clone $imports)
                ->where('file_status_id', '>', 1)
                ->where('file_service', 1)
                ->count(),

            'file_import_balance_generate' => (clone $imports)
                ->where('file_status_id', 3)
                ->where('file_step_id', 2)
                ->where('file_service', 1)
                ->count(),

            'all_file_import_generate' => (clone $imports)
                ->where('file_status_id', 3)
                ->where('file_step_id', 2)
                ->count(),

            'companies_created' => (clone $companies)
                ->where('created_at', '>=', $start)
                ->count(),

            'tree_companies_created' => (clone $treeCompanies)
                ->where('created_at', '>=', $start)
                ->where('levels', 1)
                ->count(),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
