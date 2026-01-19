<?php

namespace App\Exports;

use App\Models\ImportFile;
use App\Models\Queue\TrialBalanceData;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TrialBalanceExport extends DefaultValueBinder implements
    FromArray,
    WithHeadings,
    WithTitle,
    WithStrictNullComparison,
    WithCustomStartCell,
    WithEvents,
    WithCustomValueBinder
{
    public function __construct(
        private readonly int $fileId
    ) {}

    public function title(): string
    {
        return __('navbar.balance');
    }

    public function startCell(): string
    {
        return 'A2'; // A1 título
    }

    // Conta como texto (evita notação científica)
    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'B') { // B = Conta
            $cell->setValueExplicit((string)$value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function headings(): array
    {
        return [
            __('labels.line'),
            __('labels.account'),
            __('labels.description'),
            __('labels.previous_balance'),
            __('labels.debit'),
            __('labels.credit'),
            __('labels.monthly_activity'),
            __('labels.closing_balance'),
            __('labels.included'),
            __('labels.flag'),
            __('reports.actions'),
        ];
    }

    public function array(): array
    {
        $rows = TrialBalanceData::query()
            ->where('file_id', $this->fileId)
            ->orderBy('file_line')
            ->get([
                'file_line',
                'account',
                'description',
                'previous_balance',
                'debit',
                'credit',
                'monthly_activity',
                'closing_balance',
                'balance_included',
                'red_flag',
                'balance_decision_source',
            ]);

        $out = [];

        foreach ($rows as $r) {
            $out[] = [
                (int) $r->file_line,
                (string) $r->account,
                (string) $r->description,
                is_null($r->previous_balance) ? null : (float) $r->previous_balance,
                is_null($r->debit) ? null : (float) $r->debit,
                is_null($r->credit) ? null : (float) $r->credit,
                is_null($r->monthly_activity) ? null : (float) $r->monthly_activity,
                is_null($r->closing_balance) ? null : (float) $r->closing_balance,
                is_null($r->balance_included) ? null : ($r->balance_included ? 'Sim' : 'Não'),
                $r->red_flag ? '!' : '',
                (string) ($r->balance_decision_source ?? ''),
            ];
        }

        // Linha TOTAL (soma do saldo final) — auditoria
        $totalClosing = (float) TrialBalanceData::query()
            ->where('file_id', $this->fileId)
            ->sum('closing_balance');

        $out[] = [
            null,
            '',
            __('labels.final_balance_sum'),
            null, null, null, null,
            $totalClosing,
            '', '', '',
        ];

        return $out;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $file = ImportFile::with('company')->find($this->fileId);

                $lastCol = 'K'; // 11 colunas
                $highestRow = $sheet->getHighestRow();

                // Título centralizado na A1
                $title = __('navbar.balance'). ' — ' .
                    (($file->company->commercial_name ?? $file->company->name)) .
                    ' | ' . ($file->file_name ?? '') .
                    ' — ' . sprintf('%02d/%04d', $file->reference_month, $file->reference_year);

                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', $title);

                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(26);

                // Cabeçalho (linha 2)
                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'bottom' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // AutoFilter
                $sheet->setAutoFilter("A2:{$lastCol}{$highestRow}");

                // Freeze: header + colunas iniciais
                $sheet->freezePane('D3'); // congela A..C e linhas 1..2

                // Larguras
                $sheet->getColumnDimension('A')->setWidth(8);   // Linha
                $sheet->getColumnDimension('B')->setWidth(22);  // Conta (texto)
                $sheet->getColumnDimension('C')->setWidth(48);  // Descrição
                $sheet->getColumnDimension('D')->setWidth(16);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(14);
                $sheet->getColumnDimension('I')->setWidth(10);
                $sheet->getColumnDimension('J')->setWidth(8);
                $sheet->getColumnDimension('K')->setWidth(16);

                // Conta como texto
                $sheet->getStyle("B:B")->getNumberFormat()->setFormatCode('@');

                // Valores: 2 casas decimais (D..H)
                $sheet->getStyle("D3:H{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                // Bordas leves
                $sheet->getStyle("A2:{$lastCol}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_HAIR],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Alinhamentos
                $sheet->getStyle("A3:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("B3:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("C3:C{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("D3:H{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("I3:K{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Última linha (TOTAL) destacada
                $sheet->getStyle("A{$highestRow}:{$lastCol}{$highestRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
            }
        ];
    }
}
