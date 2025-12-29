<?php

namespace App\Livewire\Reports;

use App\Models\FileStep;
use App\Models\ImportFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UploadedFiles extends Component
{
    use WithPagination;

//    public array $form = [
//
//    ];

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $raw  = trim($this->search);
        $term = mb_strtolower($raw);

        $activeLabel   = mb_strtolower(trim(__('reports.active')));
        $inactiveLabel = mb_strtolower(trim(__('reports.inactive')));

        $files = ImportFile::query()
            ->with(['type_file', 'company', 'user'])
            ->when($term !== '', function ($q) use ($term, $activeLabel, $inactiveLabel) {

                $status = null;
                if ($this->toUp($activeLabel) == $this->toUp(__('reports.active'))) {
                    $status = 2;
                }
                if ($this->toUp($activeLabel) == $this->toUp( __('reports.inactive'))) {
                    $status = 1;
                }

                $fileStep = FileStep::query()
                    ->where('name', 'like', "%{$term}%")
                    ->pluck('id');

                $q->where(function ($qq) use ($term, $status, $fileStep) {

                    $qq->where('file_name', 'like', "%{$term}%")
                        ->orWhere('file_extension', 'like', "%{$term}%")
                        ->orWhere('reference_month', 'like', "%{$term}%")
                        ->orWhere('reference_year', 'like', "%{$term}%")
                        ->orWhereIn('file_step_id', $fileStep);

                    if (!is_null($status)) {
                        $qq->orWhere('file_status_id', $status);
                    }

                    $qq->orWhereHas('company', function ($c) use ($term) {
                        $c->where('name', 'like', "%{$term}%")
                            ->orWhere('commercial_name', 'like', "%{$term}%")
                            ->orWhere('cnpj', 'like', "%{$term}%");
                    });

                    $qq->orWhereHas('user', function ($u) use ($term) {
                        $u->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%");
                    });

                    $qq->orWhereHas('type_file', function ($t) use ($term) {
                        $t->where('name', 'like', "%{$term}%");
                    });
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.reports.uploaded-files',[
            'files' => $files
        ])->layout('layouts.app');
    }

    public function download()
    {
        return '';
    }

    public function toUp($string) : string
    {
        return strtoupper($string);
    }
}
