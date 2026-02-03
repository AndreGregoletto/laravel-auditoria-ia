<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RagWorkbookExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $result
    ) {}

    public function sheets(): array
    {
        return [
            new RagCoverSheetExport($this->result['companyName'], $this->result['periodRange']), // CAPA
            new RagBpSheetExport($this->result),    // BP
//            new RagDreSheetExport($this->result),   // DRE
            new RagViewExport($this->result),       // BALANCETE
        ];
    }
}
