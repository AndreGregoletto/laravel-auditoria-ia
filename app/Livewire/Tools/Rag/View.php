<?php

namespace App\Livewire\Tools\Rag;

use App\Services\GenerateRAG;
use Livewire\Component;

class View extends Component
{
    public string $files;

    public function mount(string $files): void
    {
        $this->files = $files;
    }

    public function render()
    {
        $service = new GenerateRAG();
        $result  = $service->startProcess(explode('|', $this->files));
//        dd($result);
        return view('livewire.tools.rag.view', ['result' => $result])->layout('layouts.app');
    }
}
