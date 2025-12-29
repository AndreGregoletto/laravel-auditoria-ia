<?php

namespace App\Livewire\Reports;

use App\Models\Company;
use App\Models\FileStatus;
use App\Models\FileStep;
use App\Models\ImportFile;
use App\Models\TypeFile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UploadedFiles extends Component
{
    use WithPagination;

    public string $filterFileName = '';
    public string $filterUser = '';
    public string $filterCompany = '';
    public string $filterExtension = '';

    public string $filterService = '';
    public string $filterStep = '';
    public string $filterStatus = '';

    public string $filterMonth = '';
    public string $filterYear  = '';

    protected $queryString = [
        'filterFileName' => ['except' => ''],
        'filterUser'     => ['except' => ''],
        'filterCompany'  => ['except' => ''],
        'filterExtension'=> ['except' => ''],
        'filterService'  => ['except' => ''],
        'filterStep'     => ['except' => ''],
        'filterStatus'   => ['except' => ''],
        'filterMonth'    => ['except' => ''],
        'filterYear'     => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updated($name, $value): void
    {
        if (str_starts_with($name, 'filter')) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset([
            'filterFileName', 'filterUser', 'filterCompany', 'filterExtension',
            'filterService', 'filterStep', 'filterStatus',
            'filterMonth', 'filterYear'
        ]);

        $this->resetPage();
    }

    public function render()
    {
        $fileStatus = FileStatus::get();
        $fileStep   = FileStep::get();
        $typeFile   = TypeFile::get();

        $query = ImportFile::query()->with(['type_file', 'company', 'user']);

        if ($t = trim($this->filterFileName)) {
            $query->where('file_name', 'like', "%{$t}%");
        }

        if ($t = trim($this->filterUser)) {
            $idUser = User::where("name", 'like', "%$t%")->pluck('id');
            $query->whereIn('user_id', $idUser);
        }

        if ($t = trim($this->filterCompany)) {
            $idCompany = Company::where("name", 'like', "%$t%")->pluck('id');
            $query->whereIn('company_id', $idCompany);
        }

        if (!empty($this->filterService)) {
            $query->where('file_service', $this->filterService);
        }

        if (!empty($this->filterStep)) {
            $query->where('file_step_id', $this->filterStep);
        }

        if (!empty($this->filterStatus)) {
            $query->where('file_status_id', $this->filterStatus);
        }

        if ($t = trim($this->filterExtension)) {
            $query->where('file_extension', 'like', "%{$t}%");
        }

        if ($t = trim($this->filterMonth)) {
            $query->where('reference_month', 'like', "%$t%");
        }

        if ($t = trim($this->filterYear)) {
            $query->where('reference_year', 'like', "%$t%");
        }

        $files = $query
            ->latest()
            ->paginate(10);

        return view('livewire.reports.uploaded-files',[
            'files'      => $files,
            'fileStep'   => $fileStep,
            'typeFile'   => $typeFile,
            'fileStatus' => $fileStatus,
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
