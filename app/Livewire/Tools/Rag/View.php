<?php

namespace App\Livewire\Tools\Rag;

use App\Exports\RagViewExport;
use App\Services\GenerateRAG;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class View extends Component
{
    public string $files;
    public array $result = [];

    public function mount(string $files): void
    {
        $this->files = $files;

        $service = new GenerateRAG();
        $this->result = $service->startProcess(explode('|', $this->files));
    }


    public function downloadXlsx()
    {
        $companySafe = preg_replace('/[^A-Za-z0-9_\- ]+/', '', (string)($this->result['name']));
        $filename = trim($companySafe) !== '' ? $companySafe : 'RAG';
        $filename = str_replace(' ', '_', $filename) . '.xlsx';

        return Excel::download(new RagViewExport($this->result), $filename);
    }

    public function render()
    {
        return view('livewire.tools.rag.view', [
            'result' => $this->result,
        ])->layout('layouts.app');
    }
}
