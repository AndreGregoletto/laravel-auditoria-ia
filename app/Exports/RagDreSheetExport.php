<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class RagDreSheetExport implements WithTitle, WithEvents
{
    public function __construct(private readonly array $result) {}

    public function title(): string
    {
        return '03 | DRE';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
            }
        ];
    }
}
