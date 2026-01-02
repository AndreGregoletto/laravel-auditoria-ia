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
            ->where('file_step_id', 5)
            ->where('file_status_id', 2)
            ->firstOrFail();

        $file->update(['file_status_id', 1, 'file_step_id' => 4]);
        session()->flash('success', __('success.file_cancel'));
    }

    public function render()
    {
        return view('livewire.reports.imports.my-files', [
            'files' => ImportFile::whereUserId(Auth::id())
                ->with('company')
                ->with('type_file')
                ->latest()
                ->get()
        ]);
    }
}
