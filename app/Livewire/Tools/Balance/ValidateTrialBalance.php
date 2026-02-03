<?php

namespace App\Livewire\Tools\Balance;

use App\Models\Company;
use App\Models\ImportFile;
use Livewire\Component;

class ValidateTrialBalance extends Component
{
    public ?int $companyId = null;

    public ?int $refMonthFrom = null;
    public ?int $refYearFrom  = null;

    public ?int $refMonthTo = null;
    public ?int $refYearTo  = null;

    public string $search = '';

    public bool $onlyMyFiles = false;

    public function mount(): void
    {
        $currentYear = now()->year;
        $this->onlyMyFiles = true;

        $this->refYearFrom = $currentYear;
        $this->refYearTo   = $currentYear;

        $this->refMonthFrom = 1;
        $this->refMonthTo   = now()->month;
    }

    public function render()
    {
        $companies = Company::query()
            ->select(['id', 'name', 'commercial_name'])
            ->orderByRaw("COALESCE(commercial_name, name)")
            ->get();

        $files = $this->filtersReady()
            ? $this->queryFiles()->get()
            : collect();
//dd($files);
        return view('livewire.tools.balance.validate-trial-balance', [
            'companies' => $companies,
            'files'     => $files,
            'months'    => $this->months(),
            'currentYear' => now()->year,
        ])->layout('layouts.app');
    }

    private function filtersReady(): bool
    {
        return !empty($this->companyId)
            && !empty($this->refMonthFrom) && !empty($this->refYearFrom)
            && !empty($this->refMonthTo)   && !empty($this->refYearTo);
    }

    private function months(): array
    {
        return [
            1 => __('labels.january')   ?? 'Janeiro',
            2 => __('labels.february')  ?? 'Fevereiro',
            3 => __('labels.march')     ?? 'Março',
            4 => __('labels.april')     ?? 'Abril',
            5 => __('labels.may')       ?? 'Maio',
            6 => __('labels.june')      ?? 'Junho',
            7 => __('labels.july')      ?? 'Julho',
            8 => __('labels.august')    ?? 'Agosto',
            9 => __('labels.september') ?? 'Setembro',
            10 => __('labels.october')  ?? 'Outubro',
            11 => __('labels.november') ?? 'Novembro',
            12 => __('labels.december') ?? 'Dezembro',
        ];
    }

    private function queryFiles()
    {
        $from = ((int)$this->refYearFrom * 100) + (int)$this->refMonthFrom;
        $to   = ((int)$this->refYearTo   * 100) + (int)$this->refMonthTo;

        $q = ImportFile::query()
            ->where('company_id', $this->companyId)
            ->where('file_service', 1)
            ->where('file_step_id', '2')
            ->whereRaw('(reference_year * 100 + reference_month) BETWEEN ? AND ?', [$from, $to])
            ->when($this->onlyMyFiles, fn($qq) => $qq->where('user_id', auth()->id()))
            ->when($this->search !== '', function ($qq) {
                $term = trim($this->search);
                $qq->where(function ($w) use ($term) {
                    $w->where('file_name', 'like', "%{$term}%");
                });
            })
            ->orderBy('reference_year', 'asc')
            ->orderBy('reference_month', 'asc')
            ->orderByDesc('id');
        return $q;
    }
}
