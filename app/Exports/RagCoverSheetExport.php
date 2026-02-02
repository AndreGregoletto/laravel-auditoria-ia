<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class RagCoverSheetExport implements WithTitle, WithEvents
{
    public function __construct(private readonly array $result) {}

    public function title(): string
    {
        return '01 | CAPA';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $name  = (string)($this->result['name'] ?? 'RAG');
                $sheet->setCellValue('B2', $name);
            }
        ];
    }
}
