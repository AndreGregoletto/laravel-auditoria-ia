<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyTree;
use App\Models\ImportFile;

class DashboardMetricsService
{
    public function getBasicStats(?int $fileService = null): array
    {
        $companies  = Company::all();
        $trees      = CompanyTree::query()
            ->select('id', 'company_tree_id', 'company_parent_id', 'levels', 'status')
            ->where('levels', 1)
            ->with(['company'])->get();

        $imports    = ImportFile::query();

        if(!is_null($fileService)){
            $imports->where('file_service', $fileService);
        }

        $pendingCount = (clone $imports)
            ->where('file_status_id', 2)
            ->where('file_step_id', 5)
            ->count();

        $processingCount = (clone $imports)
            ->where('file_status_id', 2)
            ->where('file_step_id', 1)
            ->count();

        $doneCount = (clone $imports)
            ->where('file_step_id', 2)
            ->count();

        $failedCount = (clone $imports)
            ->where('file_step_id', 3)
            ->count();

        return [
            'companies_total'     => $companies->count(),
            'companies'           => $companies,
            'trees_total'         => $trees->count(),
            'trees'               => $trees,
            'queue_pending'       => $pendingCount,
            'queue_processing'    => $processingCount,
            'queue_total_open'    => $pendingCount + $processingCount,
            'imports_done'        => $doneCount,
            'imports_failed'      => $failedCount,
            'file_service_filter' => $fileService,
        ];
    }
}
