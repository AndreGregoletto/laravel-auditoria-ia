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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

/**
 * Aba “02 | BP”
 */
class RagBpSheetExport implements
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
    private array $catalog;

    public function __construct(private readonly array $result)
    {
        $this->bp      = (array) ($result['BP'] ?? []);
        $this->catalog = (array) ($result['BP_CATALOG'] ?? []);

        $this->descByAccount = [];
        foreach ($this->catalog as $bucket => $items) {
            foreach ((array) $items as $code => $name) {
                $this->descByAccount[(string) $code] = (string) $name;
            }
        }

        $this->periods = $this->sortedPeriods($this->extractPeriodsFromBp($this->bp));
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
            '',      // separador
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
                'Ativo Circulante'     => ['currentAssets'],
                'Ativo Não Circulante' => ['nonCurrentAssets', 'nomCurrentAssets'],
                'TOTAL ATIVO'          => ['assets'],
            ],
            'PASSIVO' => [
                'Passivo Circulante'     => ['currentLiabilities'],
                'Passivo Não Circulante' => ['nonCurrentLiabilities', 'nomCurrentLiabilities'],
                'Patrimônio Líquido'     => ['freeHeritage'],
                'TOTAL PASSIVO'          => ['liabilities'],
            ],
        ];

        foreach ($blocks as $blockTitle => $sections) {

            $rows[] = [$blockTitle, null, null, null, null, null, null, null];

            foreach ($sections as $label => $keys) {

                $keys = (array) $keys;

                if (in_array('assets', $keys, true) || in_array('liabilities', $keys, true)) {
                    $keyTotal = in_array('assets', $keys, true) ? 'assets' : 'liabilities';

                    $vFinal = (float) (($this->bp[$keyTotal][$final]['sum'] ?? 0.0));
                    $vInit  = (float) (($this->bp[$keyTotal][$init]['sum'] ?? 0.0));

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

                $bucketKey = null;
                foreach ($keys as $k) {
                    if (array_key_exists($k, $this->bp)) {
                        $bucketKey = $k;
                        break;
                    }
                }

                $catalogKey = null;
                foreach ($keys as $k) {
                    if (!empty($this->catalog[$k])) {
                        $catalogKey = $k;
                        break;
                    }
                }

                $accounts = $catalogKey ? array_keys((array) $this->catalog[$catalogKey]) : [];
                sort($accounts, SORT_NATURAL);

                foreach ($accounts as $acc) {
                    $desc = $this->descByAccount[(string) $acc] ?? '';

                    $vFinal = (float) (($this->bp[$bucketKey][$final][$acc] ?? 0.0));
                    $vInit  = (float) (($this->bp[$bucketKey][$init][$acc] ?? 0.0));

                    [$var, $varPct] = $this->variation($vFinal, $vInit);

                    $rows[] = [
                        '',
                        (string) $acc,
                        $desc,
                        $this->num($vFinal),
                        null,
                        $this->num($vInit),
                        $this->num($var),
                        $varPct,
                    ];
                }

                if ($label === 'Patrimônio Líquido') {

                    $dreFinal = (float) ($this->bp['freeHeritage'][$final]['DRE'] ?? (-(float) ($this->bp['bpAll'][$final]['sum'] ?? 0.0)));
                    $dreInit  = (float) ($this->bp['freeHeritage'][$init]['DRE']  ?? (-(float) ($this->bp['bpAll'][$init]['sum'] ?? 0.0)));

                    [$varAll, $varPctAll] = $this->variation($dreFinal, $dreInit);

                    $rows[] = [
                        '',
                        'DRE',
                        'Resultado do Exercício (DRE)',
                        $this->num($dreFinal),
                        null,
                        $this->num($dreInit),
                        $this->num($varAll),
                        $varPctAll,
                    ];

                    $rows[] = [null, null, null, null, null, null, null, null];
                }

                $sumFinal = (float) (($this->bp[$bucketKey][$final]['sum'] ?? 0.0));
                $sumInit  = (float) (($this->bp[$bucketKey][$init]['sum'] ?? 0.0));

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

                $title = 'BP';
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

                $sheet->getColumnDimension('A')->setWidth(22);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(54);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(3);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(18);
                $sheet->getColumnDimension('H')->setWidth(14);

                $sheet->getStyle("B:B")->getNumberFormat()->setFormatCode('@');

                // Numéricos
                $sheet->getStyle("D3:D{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("F3:F{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle("G3:G{$highestRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                // Percentual
                $sheet->getStyle("H3:H{$highestRow}")->getNumberFormat()->setFormatCode('0.00%');

                // Alinhamentos
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
                    $a = (string) $sheet->getCell("A{$r}")->getValue();

                    if (in_array($a, ['ATIVO', 'PASSIVO'], true)) {
                        $sheet->mergeCells("A{$r}:{$lastCol}{$r}");
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFEFEFEF'],
                            ],
                        ]);
                        continue;
                    }

                    $b = (string) $sheet->getCell("B{$r}")->getValue();
                    $c = (string) $sheet->getCell("C{$r}")->getValue();
                    $d = $sheet->getCell("D{$r}")->getValue();

                    if ($a !== '' && $b === '' && $c === '' && ($d === null || $d === '')) {
                        $sheet->mergeCells("A{$r}:{$lastCol}{$r}");
                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                            'font' => ['bold' => true],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        ]);
                        continue;
                    }

                    if (str_starts_with($a, 'Subtotal ') || str_starts_with($a, 'TOTAL ')) {
                        $sheet->mergeCells("A{$r}:C{$r}");

                        $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                            'font' => ['bold' => true],
                            'borders' => [
                                'top' => ['borderStyle' => Border::BORDER_THIN],
                                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFFFFDF5'],
                            ],
                        ]);
                    }
                }
            }
        ];
    }

    private function extractPeriodsFromBp(array $bp): array
    {
        $periods = [];

        foreach ($bp as $section) {
            if (!is_array($section)) continue;

            foreach ($section as $period => $payload) {
                if (is_string($period) && preg_match('~^\d{1,2}/\d{4}$~', $period)) {
                    $periods[$period] = true;
                }
            }
        }

        return array_keys($periods);
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

    private function variation($final, $init): array
    {
        $final = is_null($final) ? null : (float) $final;
        $init  = is_null($init)  ? null : (float) $init;

        if ($final === null && $init === null) {
            return [null, null];
        }

        $var = ($final ?? 0.0) - ($init ?? 0.0);

        if ($init === null || abs($init) < 0.0000001) {
            if ($final === null || abs($final) < 0.0000001) {
                return [$var, 0.0];
            }

            return [$var, null];
        }

        $pct = $var / abs($init);
        return [$var, $pct];
    }

    private function num($v): ?float
    {
        return is_null($v) ? null : (float) $v;
    }
}
