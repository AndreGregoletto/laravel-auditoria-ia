<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\DefaultValueBinder;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RagViewExport extends DefaultValueBinder implements
    FromArray,
    WithHeadings,
    WithTitle,
    WithStrictNullComparison,
    WithCustomStartCell,
    WithEvents,
    WithCustomValueBinder
{
    public function __construct(
        private readonly array $result
    ) {}

    public function orderColumns(): array
    {
        $periods = [];

        foreach ($this->result['fileOrder'] as $file) {
            $periods[] = "{$file['reference_month']}/{$file['reference_year']}";
        }

        return $periods;
    }

    public function title(): string
    {
        return 'RAG';
    }

    /**
     * Começa em A2 para deixar A1 livre pro título.
     */
    public function startCell(): string
    {
        return 'A2';
    }

    /**
     * Força "Conta contábil" (A) e "Conta limpa" (B) como TEXTO.
     * Isso evita notação científica.
     */
    public function bindValue(Cell $cell, $value): bool
    {
        $col = $cell->getColumn(); // 'A', 'B', ...
        if (in_array($col, ['A', 'B'], true)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function headings(): array
    {
        return array_merge(
            [__('labels.account'), __('labels.clean_account'), __('labels.description')],
            $this->orderColumns()
        );
    }

    public function array(): array
    {
        $periods = $this->orderColumns();
        $rows    = [];

        foreach (($this->result['response'] ?? []) as $account => $value) {
            $line = [
                (string) $account,
                (string) ($value['clear_account'] ?? ''),
                (string) ($value['description'] ?? ''),
            ];

            foreach ($this->result['fileOrder'] as $file) {
                $v = $value['balance']["{$file['reference_month']}/{$file['reference_year']}"] ?? null;
                $line[] = is_null($v) ? null : (float) $v;
            }

            $rows[] = $line;
        }

        $totals = ['', '', __('labels.final_balance_sum')];

        foreach ($periods as $p) {
            $totals[] = isset($this->result['aClosing'][$p]) ? (float) $this->result['aClosing'][$p] : null;
        }

        $rows[] = $totals;

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $periods = array_keys($this->result['aClosing'] ?? []);
                $lastColIndex = 3 + count($periods); // A=1, B=2, C=3 ...
                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);

                // Linha 1: título centralizado
                $title = (string)($this->result['name'] ?? 'RAG');
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

                // Header (linha 2): fundo e bold
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
                $highestRow = $sheet->getHighestRow();
                $sheet->setAutoFilter("A2:{$lastCol}{$highestRow}");

                // Freeze panes: congela 2 linhas e 3 colunas (A..C)
                // D3 = depois de Descrição e depois do header
                $sheet->freezePane('D3');

                // Larguras (estilo “saiu do sistema”)
                $sheet->getColumnDimension('A')->setWidth(20); // Conta contábil
                $sheet->getColumnDimension('B')->setWidth(18); // Conta limpa
                $sheet->getColumnDimension('C')->setWidth(48); // Descrição
                for ($i = 4; $i <= $lastColIndex; $i++) {
                    $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $sheet->getColumnDimension($col)->setWidth(14);
                }

                // Formatação: A e B como texto explícito
                $sheet->getStyle("A:A")->getNumberFormat()->setFormatCode('@');
                $sheet->getStyle("B:B")->getNumberFormat()->setFormatCode('@');

                // Colunas numéricas (D..last): 2 casas decimais
                if ($lastColIndex >= 4) {
                    $sheet->getStyle("D3:{$lastCol}{$highestRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                }

                // Bordas leves na tabela inteira (a partir da linha 2)
                $sheet->getStyle("A2:{$lastCol}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_HAIR,
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Linha TOTAL (última): destaque
                $sheet->getStyle("A{$highestRow}:{$lastCol}{$highestRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Alinhamento: descrição à esquerda, números à direita
                $sheet->getStyle("C3:C{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                if ($lastColIndex >= 4) {
                    $sheet->getStyle("D3:{$lastCol}{$highestRow}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
            }
        ];
    }
}
