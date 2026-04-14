<?php

namespace App\Exports;

use App\Models\IncomeStatement;
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

class RagDreSheetExport implements
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
    private array $codeToIdMap = [];

    public function __construct(private readonly array $result)
    {
        $this->groupedSheets = (array) ($result['groupedSheets'] ?? []);
        $this->periods = $this->sortedPeriods(array_keys($this->groupedSheets));
        $this->classify = (array) ($result['classify']['dre'] ?? []);
    }

    public function title(): string
    {
        return '03 | DRE';
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
            'RECLASSIFICAÇÃO DF.:',
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
        $layout = $this->dreLayout();

        $rows[] = ['RESULTADO', null, null, null, null, null, null, null, null, null];

        foreach ($layout as $row) {
            if ($row['type'] === 'detail') {
                $id = (int) $row['id'];

                $vFinal = $this->valueByClassification($final, $id);
                $vInit  = $this->valueByClassification($init, $id);

                $reclass  = 0.0;
                $adjusted = $vInit + $reclass;

                [$avPct, $ahPct] = $this->variationPercentPair($vFinal, $adjusted);

                $rows[] = [
                    $row['label'],
                    $row['code'],
                    $this->displayNum($vFinal),
                    $this->displayNum($vInit),
                    $this->displayNum($reclass),
                    $this->displayNum($adjusted),
                    $avPct,
                    $ahPct,
                    null,
                    null,
                ];

                continue;
            }

            if ($row['type'] === 'formula') {
                $vFinal = $this->formulaValue($row['key'], $final);
                $vInit  = $this->formulaValue($row['key'], $init);

                $reclass  = 0.0;
                $adjusted = $vInit + $reclass;

                [$avPct, $ahPct] = $this->variationPercentPair($vFinal, $adjusted);

                $rows[] = [
                    $row['label'],
                    null,
                    $this->displayNum($vFinal),
                    $this->displayNum($vInit),
                    $this->displayNum($reclass),
                    $this->displayNum($adjusted),
                    $avPct,
                    $ahPct,
                    null,
                    null,
                ];

                continue;
            }

            if ($row['type'] === 'blank') {
                $rows[] = [null, null, null, null, null, null, null, null, null, null];
            }
        }

        return $rows;
    }

    private function dreLayout(): array
    {
        $codesFromClassification = [];

        foreach ($this->classify as $codeList) {
            foreach ((array) $codeList as $code) {
                $code = trim((string) $code);

                if ($code !== '') {
                    $codesFromClassification[] = $code;
                }
            }
        }

        $expectedCodes = array_values(array_unique(array_merge(
            ['10', '10.1', '20', '30', '40', '50', '60', '70', '80', '90', '100'],
            $codesFromClassification
        )));

        $items = IncomeStatement::query()
            ->where('status', 1)
            ->whereIn('code', $expectedCodes)
            ->orderBy('sort_order')
            ->get(['id', 'code', 'name'])
            ->keyBy('code');

        foreach ($items as $item) {
            $this->codeToIdMap[(string) $item->code] = (int) $item->id;
        }

        $rows = [];

        $addDetail = function (string $code) use ($items, &$rows): void {
            if (!isset($items[$code])) {
                return;
            }

            $rows[] = [
                'type'  => 'detail',
                'id'    => (int) $items[$code]->id,
                'code'  => (string) $items[$code]->code,
                'label' => (string) $items[$code]->name,
            ];
        };

        $addDetail('10');
        $addDetail('10.1');
        $rows[] = ['type' => 'formula', 'key' => 'net_revenue', 'label' => 'RECEITA LÍQUIDA'];
        $rows[] = ['type' => 'blank'];

        $addDetail('20');
        $rows[] = ['type' => 'formula', 'key' => 'gross_profit', 'label' => 'LUCRO BRUTO'];
        $rows[] = ['type' => 'blank'];

        $addDetail('30');
        $addDetail('40');
        $addDetail('50');
        $rows[] = ['type' => 'formula', 'key' => 'operating_expenses', 'label' => 'DESPESAS OPERACIONAIS'];
        $rows[] = ['type' => 'blank'];

        $addDetail('60');
        $addDetail('70');
        $rows[] = ['type' => 'formula', 'key' => 'financial_result', 'label' => 'RESULTADO FINANCEIRO'];
        $rows[] = ['type' => 'blank'];

        $rows[] = ['type' => 'formula', 'key' => 'result_before_taxes', 'label' => 'RESULTADO ANTES DOS IMPOSTOS'];
        $rows[] = ['type' => 'blank'];

        $addDetail('80');
        $addDetail('90');
        $addDetail('100');
        $rows[] = ['type' => 'formula', 'key' => 'net_income', 'label' => 'RESULTADO LÍQUIDO'];

        return $rows;
    }

    private function valByCode(string $period, string $code): float
    {
        if (!isset($this->codeToIdMap[$code])) {
            return 0.0;
        }

        return $this->valueByClassification($period, $this->codeToIdMap[$code]);
    }

    private function formulaValue(string $key, string $period): float
    {
        return match ($key) {
            'net_revenue'         => $this->valByCode($period, '10') + $this->valByCode($period, '10.1'),
            'gross_profit'        => $this->formulaValue('net_revenue', $period) + $this->valByCode($period, '20'),
            'operating_expenses'  => $this->formulaValue('gross_profit', $period)
                                     + $this->valByCode($period, '30')
                                     + $this->valByCode($period, '40')
                                     + $this->valByCode($period, '50'),
            'financial_result'    => $this->valByCode($period, '60') + $this->valByCode($period, '70'),
            'result_before_taxes' => $this->formulaValue('operating_expenses', $period)
                                     + $this->formulaValue('financial_result', $period),
            'net_income'          => $this->formulaValue('result_before_taxes', $period)
                                     + $this->valByCode($period, '80')
                                     + $this->valByCode($period, '90')
                                     + $this->valByCode($period, '100'),
            default               => 0.0,
        };
    }

    private function valueByClassification(string $period, int $id): float
    {
        return (float) (($this->groupedSheets[$period]['dre'][$id] ?? 0.0));
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $lastCol = 'J';

                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', 'DRE');

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

                $sheet->getColumnDimension('A')->setWidth(52);
                $sheet->getColumnDimension('B')->setWidth(10);
                $sheet->getColumnDimension('C')->setWidth(16);
                $sheet->getColumnDimension('D')->setWidth(16);
                $sheet->getColumnDimension('E')->setWidth(18);
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

                    if ($a === 'RESULTADO') {
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
                        in_array($a, [
                            'RECEITA LÍQUIDA',
                            'LUCRO BRUTO',
                            'DESPESAS OPERACIONAIS',
                            'RESULTADO FINANCEIRO',
                            'RESULTADO ANTES DOS IMPOSTOS',
                            'RESULTADO LÍQUIDO',
                        ], true)
                    ) {
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                            'font' => ['bold' => true],
                            'borders' => [
                                'top' => ['borderStyle' => Border::BORDER_THIN],
                                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                            ],
                        ]);
                        continue;
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
}