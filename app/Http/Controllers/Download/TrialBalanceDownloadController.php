<?php

namespace App\Http\Controllers\Download;

use App\Exports\TrialBalanceExport;
use App\Models\ImportFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class TrialBalanceDownloadController extends Controller
{
    public function xlsx(ImportFile $file)
    {
        // (Opcional) autorização
        // $this->authorize('view', $file);

        $filename = 'Balancete_' .
            preg_replace('/[^A-Za-z0-9_\-]+/', '_', $file->company->name ?? 'Empresa') . '_' .
            sprintf('%02d_%04d', $file->reference_month, $file->reference_year) . '.xlsx';

        return Excel::download(new TrialBalanceExport($file->id), $filename);
    }

    public function xlsxIncluded(\App\Models\ImportFile $file)
    {
        $filename = 'Balancete_INCLUIDAS_' .
            preg_replace('/[^A-Za-z0-9_\-]+/', '_', $file->company->name ?? 'Empresa') . '_' .
            sprintf('%02d_%04d', $file->reference_month, $file->reference_year) . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TrialBalanceIncludeExport($file->id, true),
            $filename
        );
    }

}
