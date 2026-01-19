<?php

namespace App\Http\Controllers\Download;

use App\Exports\RagViewExport;
use App\Http\Controllers\Controller;
use App\Services\GenerateRAG;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RagDownloadController extends Controller
{
    public function xlsx(string $files)
    {
        $service = new GenerateRAG();
        $result = $service->startProcess(explode('|', $files));

        $name = (string)($result['name'] ?? 'RAG');
        $companySafe = preg_replace('/[^A-Za-z0-9_\- ]+/', '', $name);
        $filename = trim($companySafe) !== '' ? $companySafe : 'RAG';
        $filename = str_replace(' ', '_', $filename) . '.xlsx';

        return Excel::download(new RagViewExport($result), $filename);
    }
}
