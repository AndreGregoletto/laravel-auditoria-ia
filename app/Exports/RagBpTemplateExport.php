<?php

namespace App\Exports;

use DateTime;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RagBpTemplateExport implements
    FromArray,
    WithHeadings,
    WithTitle,
    WithStrictNullComparison,
    WithCustomStartCell,
    WithEvents
{
    private array $bp;
    private array $periods;
    private array $descByAccount;

    public function __construct(private readonly array $result)
    {
        $this->bp = (array)($result['BP'] ?? []);

        $this->descByAccount = [];
        foreach (($result['response'] ?? []) as $acc => $row) {
            $this->descByAccount[(string)$acc] = (string)($row['description'] ?? '');
        }

        $this->periods = $this->sortedPeriods(array_keys((array)($this->bp['assets'] ?? [])));
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
            'Grupo',
            __('labels.account'),
            __('labels.description'),
            $final,
            '',
            $init,
            __('labels.variation') ?? 'Variação',
            __('labels.variation_percent') ?? 'Variação %',
        ];
    }

    public function array(): array
    {
        if (count($this->periods) < 1) {
            return [];
        }

        $init  = $this->periods[0];
        $final = $this->periods[1] ?? $this->periods[0];

        $rows = [];

        $blocks = [
            'ATIVO' => [
                'Ativo Circulante'     => 'currentAssets',
                'Ativo Não Circulante' => ['nonCurrentAssets', 'nomCurrentAssets'],
                'TOTAL ATIVO'          => 'assets',
            ],
            'PASSIVO' => [
                'Passivo Circulante'       => 'currentLiabilities',
                'Passivo Não Circulante'   => ['nonCurrentLiabilities', 'nomCurrentLiabilities'],
                'Patrimônio Líquido'       => 'freeHeritage',
                'TOTAL PASSIVO'            => 'liabilities',
            ],
        ];

        foreach ($blocks as $blockTitle => $sections) {

            $rows[] = [$blockTitle, null, null, null, null, null, null, null];

            foreach ($sections as $label => $key) {
                $keys = (array) $key;

                $dataByPeriod = [];
                foreach ($keys as $k) {
                    if (!empty($this->bp[$k])) {
                        $dataByPeriod = (array) $this->bp[$k];
                        break;
                    }
                }

                if (in_array($key, ['assets', 'liabilities'], true)) {
                    $vFinal = $dataByPeriod[$final]['sum'] ?? null;
                    $vInit  = $dataByPeriod[$init]['sum'] ?? null;
                    [$var, $varPct] = $this->variation($vFinal, $vInit);

                    $rows[] = [
                        $label,
                        null,
                        null,
                        $this->num($vFinal),
                        null,
                        $this->num($vInit),
                        $this->num($var),
                        $varPct,
                    ];

                    $rows[] = [null, null, null, null, null, null, null, null];
                    continue;
                }

                $rows[] = [$label, null, null, null, null, null, null, null];

                $accounts = $this->collectAccountsFromSection($dataByPeriod, $init, $final);

                foreach ($accounts as $acc) {
                    $desc = $this->descByAccount[$acc] ?? '';

                    $vFinal = $dataByPeriod[$final][$acc] ?? null;
                    $vInit  = $dataByPeriod[$init][$acc] ?? null;
                    [$var, $varPct] = $this->variation($vFinal, $vInit);

                    $rows[] = [
                        '',
                        $acc,
                        $desc,
                        $this->num($vFinal),
                        null,
                        $this->num($vInit),
                        $this->num($var),
                        $varPct,
                    ];
                }

                $sumFinal = $dataByPeriod[$final]['sum'] ?? null;
                $sumInit  = $dataByPeriod[$init]['sum'] ?? null;
                [$var, $varPct] = $this->variation($sumFinal, $sumInit);

                $rows[] = [
                    "Subtotal {$label}",
                    null,
                    null,
                    $this->num($sumFinal),
                    null,
                    $this->num($sumInit),
                    $this->num($var),
                    $varPct,
                ];

                $rows[] = [null, null, null, null, null, null, null, null];
            }
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();
                $lastCol = 'H';

                $title = 'BP — title';
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

                $sheet->setAutoFilter("A2:{$lastCol}{$highestRow}");

                $sheet->freezePane('D3');

                $sheet->getColumnDimension('A')->setWidth(22); // Grupo
                $sheet->getColumnDimension('B')->setWidth(22); // Conta
                $sheet->getColumnDimension('C')->setWidth(54); // Descrição
                $sheet->getColumnDimension('D')->setWidth(18); // Final
                $sheet->getColumnDimension('E')->setWidth(3);  // separador
                $sheet->getColumnDimension('F')->setWidth(18); // Inicial
                $sheet->getColumnDimension('G')->setWidth(18); // Variação
                $sheet->getColumnDimension('H')->setWidth(14); // Variação %

                $sheet->getStyle("B:B")->getNumberFormat()->setFormatCode('@');

                $sheet->getStyle("D3:D{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("F3:F{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("G3:G{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                $sheet->getStyle("H3:H{$highestRow}")->getNumberFormat()->setFormatCode('0.00%');

                $sheet->getStyle("C3:C{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("D3:D{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("F3:F{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("G3:G{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("H3:H{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

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

                for ($r = 3; $r <= $highestRow; $r++) {
                    $a = (string)$sheet->getCell("A{$r}")->getValue();

                    if (in_array($a, ['ATIVO', 'PASSIVO'], true)) {
                        $sheet->mergeCells("A{$r}:{$lastCol}{$r}");
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        ]);
                        continue;
                    }

                    $b = (string)$sheet->getCell("B{$r}")->getValue();
                    $c = (string)$sheet->getCell("C{$r}")->getValue();
                    if ($a !== '' && $b === '' && $c === '') {
                        $sheet->mergeCells("A{$r}:{$lastCol}{$r}");
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        ]);
                        continue;
                    }

                    if (str_starts_with($a, 'Subtotal ') || str_starts_with($a, 'TOTAL ')) {
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                            'font' => ['bold' => true],
                            'borders' => [
                                'top' => ['borderStyle' => Border::BORDER_THIN],
                            ],
                        ]);
                    }
                }
            }
        ];
    }

    private function collectAccountsFromSection(array $dataByPeriod, string $init, string $final): array
    {
        $accounts = [];

        foreach ([$init, $final] as $p) {
            $arr = (array)($dataByPeriod[$p] ?? []);
            foreach ($arr as $k => $v) {
                if ($k === 'sum') continue;
                $accounts[(string)$k] = true;
            }
        }

        $list = array_keys($accounts);
        sort($list, SORT_NATURAL);
        return $list;
    }

    private function sortedPeriods(array $periods): array
    {
        usort($periods, function ($a, $b) {
            $da = DateTime::createFromFormat('m/Y', (string)$a) ?: null;
            $db = DateTime::createFromFormat('m/Y', (string)$b) ?: null;
            $ta = $da ? (int)$da->format('Ym') : 0;
            $tb = $db ? (int)$db->format('Ym') : 0;
            return $ta <=> $tb;
        });

        return array_values($periods);
    }

    private function variation($final, $init): array
    {
        $final = is_null($final) ? null : (float)$final;
        $init  = is_null($init)  ? null : (float)$init;

        if ($final === null && $init === null) {
            return [null, null];
        }

        $var = ($final ?? 0.0) - ($init ?? 0.0);

        if ($init === null || abs($init) < 0.0000001) {
            return [$var, null];
        }

        $pct = $var / abs($init);
        return [$var, $pct];
    }

    private function num($v): ?float
    {
        return is_null($v) ? null : (float)$v;
    }
}
