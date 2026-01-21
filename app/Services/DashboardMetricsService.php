<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyTree;
use App\Models\ImportFile;

class DashboardMetricsService
{
    public function getStats(): array
    {
        $imports = ImportFile::query();

        $processing = (clone $imports)
            ->where('file_status_id', 2)
            ->where('file_step_id', 1)
            ->count();

        $processed = (clone $imports)
            ->whereIn('file_status_id', [2,3])
            ->where('file_step_id',  1)
            ->count();

        $error = (clone $imports)
            ->where('file_step_id', 3)
            ->count();

        $cancelled = (clone $imports)
            ->where('file_step_id', 4)
            ->count();

        $in_queue = (clone $imports)
            ->where('file_status_id', 2)
            ->where('file_step_id', 5)
            ->count();

        $balance_import = (clone $imports)
            ->where('file_status_id', 2)
            ->where('file_service', 1)
            ->where('file_step_id', 4)
            ->count();

        $balance_validated = (clone $imports)
            ->where('file_status_id', 3)
            ->where('file_service', 1)
            ->where('file_step_id', 2)
            ->count();

        return [
            'queue' => [
                __('reports.processing')             => $processing,
                __('reports.processed')              => $processed,
                __('reports.error')                  => $error,
                __('reports.cancelled')              => $cancelled,
                __('reports.in_queue')               => $in_queue,
                __('services.balance')               => $balance_import,
                __('labels.validated_trial_balance') => $balance_validated,
            ],
        ];
    }
}
