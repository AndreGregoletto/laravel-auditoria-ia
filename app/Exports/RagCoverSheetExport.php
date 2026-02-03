<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\SheetView;

class RagCoverSheetExport implements WithTitle, WithEvents
{
    public function __construct(
        private readonly string $companyName,
        private readonly string $periodRange
    ) {}

    public function title(): string
    {
        return '01 | Capa';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setShowGridlines(false);

                $sheet->getSheetView()->setView(SheetView::SHEETVIEW_NORMAL);

                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(100);


                // Título Principal
                $rowTitle = 12;
                $sheet->setCellValue("B{$rowTitle}", 'Pré-Auditoria das Demonstrações Contábeis e Análises de Índices');

                // Subtítulo
                $rowSub = 13;
                $sheet->setCellValue("B{$rowSub}", 'RAG - ANÁLISE ECONÔMICO-FINANCEIRA');

                // Nome da Empresa
                $rowCompany = 20;
                $sheet->setCellValue("B{$rowCompany}", mb_strtoupper($this->companyName));

                // Período
                $rowPeriod = 22;
                $sheet->setCellValue("B{$rowPeriod}", "Período: " . $this->periodRange);

                // Data de Geração
                $rowFooter = 40;
                $sheet->setCellValue("B{$rowFooter}", 'Gerado em: ' . date('d/m/Y H:i'));

                // 3. Estilização

                // Título
                $sheet->getStyle("B{$rowTitle}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 24, 'color' => ['argb' => 'FF333333'], 'name' => 'Calibri'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Subtítulo
                $sheet->getStyle("B{$rowSub}")->applyFromArray([
                    'font' => ['size' => 14, 'color' => ['argb' => 'FF808080'], 'name' => 'Calibri'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Empresa
                $sheet->getStyle("B{$rowCompany}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 28, 'color' => ['argb' => 'FF000000'], 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Período
                $sheet->getStyle("B{$rowPeriod}")->applyFromArray([
                    'font' => ['size' => 16, 'italic' => true, 'color' => ['argb' => 'FF555555']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Rodapé
                $sheet->getStyle("B{$rowFooter}")->applyFromArray([
                    'font' => ['size' => 9, 'color' => ['argb' => 'FFAAAAAA']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Fundo Branco
                $sheet->getStyle('A1:Z100')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFFFF']],
                ]);
            },
        ];
    }
}
