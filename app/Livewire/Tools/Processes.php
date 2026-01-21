<?php

namespace App\Livewire\Tools;

use App\Livewire\Tools\Rag\View;
use App\Models\Company;
use App\Models\ImportFile;
use App\Services\GenerateRAG;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Processes extends Component
{
    public ?int $companyId = null;
    public bool $onlyMyFiles = false;

    public ?int $refMonthFrom = null;
    public ?int $refYearFrom  = null;
    public ?int $refMonthTo   = null;
    public ?int $refYearTo    = null;

    public array $availableSelectedIds = [];
    public array $selectedFileIds = [];

    public function mount(): void
    {
        $this->refMonthFrom = (int) now()->month;
        $this->refYearFrom  = (int) now()->year;

        $this->refMonthTo   = (int) now()->month;
        $this->refYearTo    = (int) now()->year;

        $this->onlyMyFiles = true;
    }

    public function filtersReady(): bool
    {
        return filled($this->companyId)
            && filled($this->refMonthFrom) && filled($this->refYearFrom)
            && filled($this->refMonthTo)   && filled($this->refYearTo);
    }

    public function getAvailableFilesProperty()
    {
        if (
            empty($this->companyId) ||
            empty($this->refMonthFrom) || empty($this->refYearFrom) ||
            empty($this->refMonthTo)   || empty($this->refYearTo)
        ) {
            return collect();
        }

        $from = ((int) $this->refYearFrom * 100) + (int) $this->refMonthFrom;
        $to   = ((int) $this->refYearTo   * 100) + (int) $this->refMonthTo;

        $q = ImportFile::query()
            ->where('file_step_id', 2)
            ->where('file_status_id', 3)
            ->where('company_id', $this->companyId)
            ->when($this->onlyMyFiles, fn ($qq) => $qq->where('user_id', Auth::id()))
            ->whereRaw('(reference_year * 100 + reference_month) BETWEEN ? AND ?', [$from, $to])
            ->when(!empty($this->selectedFileIds), fn ($qq) => $qq->whereNotIn('id', $this->selectedFileIds))
            ->orderBy('reference_year')
            ->orderBy('reference_month')
            ->orderByDesc('id');

        return $q->get([
            'id',
            'file_name',
            'reference_month',
            'reference_year',
            'created_at',
        ]);
    }

    public function getSelectedFilesProperty()
    {
        if (empty($this->selectedFileIds)) {
            return collect();
        }

        return ImportFile::query()
            ->whereIn('id', $this->selectedFileIds)
            ->orderBy('reference_year')
            ->orderBy('reference_month')
            ->orderByDesc('id')
            ->get([
                'id',
                'reference_month',
                'reference_year',
                'file_name',
                'file_extension',
                'file_step_id',
                'file_status_id',
                'error_log',
            ]);
    }

    public function addSelected(): void
    {
        if (empty($this->availableSelectedIds)) {
            return;
        }

        $this->selectedFileIds = array_values(array_unique(array_merge(
            $this->selectedFileIds,
            array_map('intval', $this->availableSelectedIds)
        )));

        $this->availableSelectedIds = [];
    }

    public function removeSelected(int $id): void
    {
        $this->selectedFileIds = array_values(array_diff($this->selectedFileIds, [(int) $id]));
    }

    public function clearSelected(): void
    {
        $this->selectedFileIds      = [];
        $this->availableSelectedIds = [];
    }

    public function updated($field): void
    {
        if ($this->refMonthFrom && $this->refYearFrom && $this->refMonthTo && $this->refYearTo) {
            $from = ((int) $this->refYearFrom * 100) + (int) $this->refMonthFrom;
            $to   = ((int) $this->refYearTo   * 100) + (int) $this->refMonthTo;

            if ($from > $to) {
                $this->refMonthTo = $this->refMonthFrom;
                $this->refYearTo  = $this->refYearFrom;
            }
        }

        if (in_array($field, ['companyId','onlyMyFiles','refMonthFrom','refYearFrom','refMonthTo','refYearTo'], true)) {
            $this->availableSelectedIds = [];
        }
    }

    public function updatedCompanyId($value): void
    {
        $this->selectedFileIds = [];
        $this->availableSelectedIds = [];
    }

    public function updatedOnlyMyFiles($value): void
    {
        $this->selectedFileIds = [];
        $this->availableSelectedIds = [];
    }

    public function render()
    {
        $companies = Company::query()
            ->where('status', 1)
            ->orderByRaw("COALESCE(commercial_name, name)")
            ->get(['id', 'name', 'commercial_name']);

        return view('livewire.tools.processes', compact('companies'))
            ->layout('layouts.app');
    }

    public function generateRag()
    {
//        $service = new GenerateRAG();
//        $result  = $service->startProcess($this->selectedFileIds)
        $files = implode('|',$this->selectedFileIds);
        return redirect()->route('rag.view', ['files' => $files]);
    }

}
