<?php

namespace App\Livewire\Reports\Imports;

use App\Models\ImportFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MyFiles extends Component
{
    public function cancel(int $id)
    {
        $file = ImportFile::whereId($id)
            ->whereUserId(Auth::id())
            ->whereFileStep(0)
            ->whereStatus(1)->firstOrFail();

        $file->update(['status', 0]);
        session()->flash('success', 'Arquivo cancelado com sucesso');
    }

    public function render()
    {
        return view('livewire.reports.imports.my-files', [
            'files' => ImportFile::whereUserId(Auth::id())->latest()->get()
        ]);
    }
}
