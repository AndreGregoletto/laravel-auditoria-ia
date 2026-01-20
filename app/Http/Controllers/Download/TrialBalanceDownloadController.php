<?php

namespace App\Http\Controllers\Download;

use App\Exports\TrialBalanceExport;
use App\Models\ImportFile;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class TrialBalanceDownloadController extends Controller
{
    public string $fileName;

    public function fileName($file, $include): void
    {
        $concat = $include ? __("labels.included") : "";

        $filename = "Balancete {$concat} | " .
            preg_replace('/[^A-Za-z0-9_\-]+/', ' ', $file->company->name) . ' - ' .
            sprintf('%02d-%04d', $file->reference_month, $file->reference_year) . '.xlsx';

        $this->fileName = $filename;
    }
    public function xlsx(ImportFile $file)
    {
        $this->fileName($file, false);
        return Excel::download(new TrialBalanceExport($file->id), $this->fileName);
    }

    public function xlsxIncluded(\App\Models\ImportFile $file)
    {
        $this->fileName($file, true);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TrialBalanceIncludeExport($file->id, true),
            $this->fileName
        );
    }

}
