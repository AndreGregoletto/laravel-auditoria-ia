<?php

namespace App\Exports;

use App\Models\BalanceSheet;
use DateTime;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RagBpSheetExport implements
    FromArray,
    WithHeadings,
    WithTitle,
    WithStrictNullComparison,
    WithCustomStartCell,
    WithEvents
{
    private array $groupedSheets;
    private array $periods;
    private array $classify;

    public function __construct(private readonly array $result)
    {
        $this->groupedSheets = (array) ($result['groupedSheets'] ?? []);
        $this->periods = $this->sortedPeriods(array_keys($this->groupedSheets));
        $this->classify = $result['classify']['bp'];
    }

    public function title(): string
    {
        return '02 | BP';
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function headings(): array
    {
        $final = $this->periods[1] ?? ($this->periods[0] ?? '');
        $init  = $this->periods[0] ?? '';

        return [
            '',
            '',
            $final,
            $init,
            'AJUSTE DF.:',
            $init,
            'AV%',
            'AH%',
            'AHS',
            'ESCOPO RA',
        ];
    }

    public function array(): array
    {
        if (empty($this->periods)) {
            return [];
        }

        $init  = $this->periods[0];
        $final = $this->periods[1] ?? $this->periods[0];

        $rows = [];
        $layout = $this->bpLayout();

        foreach ($layout as $block) {
            $rows[] = [$block['title'], null, null, null, null, null, null, null, null, null];

            foreach ($block['sections'] as $section) {
                foreach ($section['items'] as $item) {
                    $id    = (int) $item['id'];
                    $code  = (string) $item['code'];
                    $label = (string) $item['label'];

                    $vFinal = $this->valueByClassification($final, $id);
                    $vInit  = $this->valueByClassification($init, $id);

                    $adjust = 0.0;
                    $adjusted = $vInit + $adjust;

                    [$avPct, $ahPct] = $this->variationPercentPair($vFinal, $adjusted);

                    $rows[] = [
                        $label,
                        $code,
                        $this->displayNum($vFinal),
                        $this->displayNum($vInit),
                        $this->displayNum($adjust),
                        $this->displayNum($adjusted),
                        $avPct,
                        $ahPct,
                        null,
                        null,
                    ];
                }

                $sumFinal = 0.0;
                $sumInit = 0.0;
                $sumAdjust = 0.0;
                $sumAdjusted = 0.0;

                foreach ($section['items'] as $item) {
                    $id = (int) $item['id'];

                    $lineFinal = $this->valueByClassification($final, $id);
                    $lineInit  = $this->valueByClassification($init, $id);
                    $lineAdjust = 0.0;
                    $lineAdjusted = $lineInit + $lineAdjust;

                    $sumFinal += $lineFinal;
                    $sumInit += $lineInit;
                    $sumAdjust += $lineAdjust;
                    $sumAdjusted += $lineAdjusted;
                }

                [$avPct, $ahPct] = $this->variationPercentPair($sumFinal, $sumAdjusted);

                $rows[] = [
                    $section['subtotal'],
                    null,
                    $this->displayNum($sumFinal),
                    $this->displayNum($sumInit),
                    $this->displayNum($sumAdjust),
                    $this->displayNum($sumAdjusted),
                    $avPct,
                    $ahPct,
                    null,
                    null,
                ];

                $rows[] = [null, null, null, null, null, null, null, null, null, null];
            }

            $blockFinal = 0.0;
            $blockInit = 0.0;
            $blockAdjust = 0.0;
            $blockAdjusted = 0.0;

            foreach ($block['sections'] as $section) {
                foreach ($section['items'] as $item) {
                    $id = (int) $item['id'];

                    $lineFinal = $this->valueByClassification($final, $id);
                    $lineInit  = $this->valueByClassification($init, $id);
                    $lineAdjust = 0.0;
                    $lineAdjusted = $lineInit + $lineAdjust;

                    $blockFinal += $lineFinal;
                    $blockInit += $lineInit;
                    $blockAdjust += $lineAdjust;
                    $blockAdjusted += $lineAdjusted;
                }
            }

            [$avPct, $ahPct] = $this->variationPercentPair($blockFinal, $blockAdjusted);

            $rows[] = [
                $block['total'],
                null,
                $this->displayNum($blockFinal),
                $this->displayNum($blockInit),
                $this->displayNum($blockAdjust),
                $this->displayNum($blockAdjusted),
                $avPct,
                $ahPct,
                null,
                null,
            ];

            $rows[] = [null, null, null, null, null, null, null, null, null, null];
            $rows[] = [null, null, null, null, null, null, null, null, null, null];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $lastCol = 'J';

                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'BP');

                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

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

                $sheet->freezePane('C3');

                $sheet->getColumnDimension('A')->setWidth(44);
                $sheet->getColumnDimension('B')->setWidth(10);
                $sheet->getColumnDimension('C')->setWidth(16);
                $sheet->getColumnDimension('D')->setWidth(16);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(16);
                $sheet->getColumnDimension('G')->setWidth(10);
                $sheet->getColumnDimension('H')->setWidth(10);
                $sheet->getColumnDimension('I')->setWidth(10);
                $sheet->getColumnDimension('J')->setWidth(18);

                $sheet->getStyle("C3:F{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                $sheet->getStyle("G3:H{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('0.00%');

                for ($r = 3; $r <= $highestRow; $r++) {
                    $a = trim((string) $sheet->getCell("A{$r}")->getValue());
                    $b = trim((string) $sheet->getCell("B{$r}")->getValue());

                    if (in_array($a, ['ATIVO', 'PASSIVO'], true)) {
                        $sheet->mergeCells("A{$r}:{$lastCol}{$r}");
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFEFEFEF'],
                            ],
                        ]);
                        continue;
                    }

                    if (
                        $a !== '' &&
                        $b === '' &&
                        str_starts_with($a, 'TOTAL ')
                    ) {
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                            'font' => ['bold' => true],
                            'borders' => [
                                'top' => ['borderStyle' => Border::BORDER_THIN],
                                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE],
                            ],
                        ]);
                        continue;
                    }

                    if ($a !== '' && $b === '' && !str_starts_with($a, 'TOTAL ')) {
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                            'font' => ['bold' => true],
                            'borders' => [
                                'top' => ['borderStyle' => Border::BORDER_THIN],
                                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                            ],
                        ]);
                    }

                    if ($b !== '') {
                        $sheet->getStyle("B{$r}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['argb' => 'FFFF0000']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }
                }
            },
        ];
    }

    private function valueByClassification(string $period, int $id): float
    {
        if ($id === 47) {
            return array_sum((array) ($this->groupedSheets[$period]['dre'] ?? []));
        }

        return (float) (($this->groupedSheets[$period]['bp'][$id] ?? 0.0));
    }

    private function bpLayout(): array
    {
        $balanceSheet = BalanceSheet::query()
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get(['id', 'side', 'section', 'code', 'name']);

        $grouped = [];

        foreach ($balanceSheet as $item) {
            $grouped[$item->side][$item->section][] = [
                'id'    => (int) $item->id,
                'code'  => (string) $item->code,
                'label' => (string) $item->name,
            ];
        }

        return [
            [
                'title' => 'ATIVO',
                'total' => 'TOTAL DO ATIVO',
                'sections' => [
                    [
                        'subtotal' => 'TOTAL ATIVO CIRCULANTE',
                        'items' => $grouped['assets']['current'] ?? [],
                    ],
                    [
                        'subtotal' => 'TOTAL ATIVO NÃO CIRCULANTE',
                        'items' => $grouped['assets']['non_current'] ?? [],
                    ],
                ],
            ],
            [
                'title' => 'PASSIVO',
                'total' => 'TOTAL DO PASSIVO',
                'sections' => [
                    [
                        'subtotal' => 'TOTAL PASSIVO CIRCULANTE',
                        'items' => $grouped['liabilities']['current'] ?? [],
                    ],
                    [
                        'subtotal' => 'TOTAL PASSIVO NÃO CIRCULANTE',
                        'items' => $grouped['liabilities']['non_current'] ?? [],
                    ],
                    [
                        'subtotal' => 'PATRIMÔNIO LÍQUIDO',
                        'items' => $grouped['equity']['equity'] ?? [],
                    ],
                ],
            ],
        ];
    }

    private function sortedPeriods(array $periods): array
    {
        usort($periods, function ($a, $b) {
            $da = DateTime::createFromFormat('m/Y', (string) $a) ?: null;
            $db = DateTime::createFromFormat('m/Y', (string) $b) ?: null;

            $ta = $da ? (int) $da->format('Ym') : 0;
            $tb = $db ? (int) $db->format('Ym') : 0;

            return $ta <=> $tb;
        });

        return array_values($periods);
    }

    private function variationPercentPair(float $final, float $base): array
    {
        $av = null;
        $ah = null;

        if (abs($base) > 0.0000001) {
            $ah = ($final - $base) / abs($base);
        }

        return [$av, $ah];
    }

    private function displayNum(?float $v): ?float
    {
        if ($v === null) {
            return null;
        }

        return abs($v) < 0.0000001 ? null : $v;
    }
}