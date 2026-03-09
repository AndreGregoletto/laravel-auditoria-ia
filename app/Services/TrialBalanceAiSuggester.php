<?php

namespace App\Services;

use App\Models\BalanceSheet;
use App\Models\ImportFile;
use App\Models\IncomeStatement;
use App\Models\PivotBalanceSheetReference;
use App\Models\PivotIncomeStatementReference;
use App\Models\Queue\TrialBalanceData;

class TrialBalanceAiSuggester
{
    public function suggestForFile(int $fileId): array
    {
        $classification = $this->getClassificationDesc($fileId);

        $rows = TrialBalanceData::query()
            ->where('file_id', $fileId)
            ->orderBy('file_line')
            ->get();

        $out = [];
        $max = 0;
        foreach ($rows as $r) {
            $included = false;
            $redflag  = 0.10;
            $eps      = 0.05;
            $debit    = (float) $r->debit;
            $credit   = (float) $r->credit;
            $monthly  = (float) $r->monthly_activity;
            $previous = (float) $r->previous_balance;
            $closing  = (float) $r->closing_balance;
            $balance_sheet    = '';
            $income_statement = '';

            if (abs(($debit - $credit) - $monthly) > $eps) {
                $redflag += 0.30;
            }

            if (abs(($previous + $monthly) - $closing) > $eps) {
                $redflag += 0.30;
            }

            if (blank($r->account) || blank($r->description)) {
                $redflag += 0.30;
            }

            $len = null;

            if (is_string($r->account) && substr_count($r->account, '.') === 4) {
                $len = $r->account
                    |> (fn($t) => explode('.', $t))
                    |> (fn($t) => end($t))
                    |> (fn($t) => mb_strlen($t));

                $included = ($len === 7);
                $max += ($len === 7) ? $closing : 0;
            }

            $confidence = null;
            if ($len !== null) {
                $base = $included ? 80 : 50;
                $confidence = (int) max(0, min(100, round($base - ($redflag * 40))));
            }

            if ($len === null) {
                $rationale = 'Sugestão automática baseada em validações contábeis básicas (sem regra de nível aplicável).';
            } else {
                $rationale = $included
                    ? "Sugestão baseada em padrão do plano: último nível com {$len} caracteres indica conta analítica."
                    : "Sugestão baseada em padrão do plano: último nível com {$len} caracteres indica conta sintética/controle.";
            }

            /*
             * Classification BP & DRE
             */
            if($included){
                $fourNumber = substr($r->account, 0, 4);
                $aBp        = ['1.1.', '1.2.', '2.1.', '2.2.', '2.4.'];

                $priority = function ($type) use ($classification, $r) {
                    foreach ($classification[$type] as $id => $acc) {
                        if (str_contains($r->account, $acc)) {
                            return $id;
                        }
                    }
                    return '';
                };

                if(in_array($fourNumber, $aBp)){
                    $balance_sheet    = $priority('bp');
                }else{
                    $income_statement = $priority('dre');
                }
            }

            $out[$r->id] = [
                'included'            => $included,
                'confidence'          => $confidence,
                'rationale'           => $rationale,
                'redflag'             => $redflag,
                'closing'             => $closing,
                'balance_sheet_id'    => $balance_sheet,
                'income_statement_id' => $income_statement,
            ];
        }

        if($max >= 1){
            $minVal = $max - 0.02;
            $maxVal = $max + 0.02;
            foreach ($out as $key => $o){
                if($o['closing'] >= $minVal && $o['closing'] <= $maxVal && $o['included']){
                    $out[$key]['included'] = false;
                    $out[$key]['confidence'] = max(0, min(100, round(50 - (0.5 * 40))));;
                    $out[$key]['rationale'] = "Removido pois o valor exato divergia com o Balancete";
                    $out[$key]['redflag'] = 0.5;
                    return $out;
                }
            }
        }

        return $out;
    }

    public function getClassification($idFile): array
    {
        $companyId = ImportFile::where('id', $idFile)->value('company_id');

        if (!$companyId) return [];

        $priority = function ($modelClass, $type) use ($companyId) {
//            $value = match ($type){
//                'bp'  => 'id',
//                'dre' => 'id',
//            };

            $result = $modelClass::where('company_id', $companyId)->get()->pluck('value', 'id');
            if ($result->isNotEmpty()) return $result->toArray();

            $result = $modelClass::where('company_tree_id', $companyId)->get()->pluck('value', 'id');
            if ($result->isNotEmpty()) return $result->toArray();

            return $modelClass::whereNull('company_id')->whereNull('company_tree_id')->get()->pluck('id', 'value')->toArray();
        };

        return [
            'bp'  => array_filter($priority(PivotBalanceSheetReference::class, 'bp')),
            'dre' => array_filter($priority(PivotIncomeStatementReference::class, 'dre')),
        ];

    }

    public function getClassificationName($idFile): array
    {
        $aClassify = $this->getClassification($idFile);

        $classify = function ($modelClass, $type) use ($aClassify) {
            if (empty($aClassify[$type])) return [];

            $ids = array_values($aClassify[$type]);

            return $modelClass::whereIn('id', $ids)
                ->get()
                ->sortBy(function ($model) use ($ids) {
                    return array_search($model->id, $ids);
                })
                ->pluck('name', 'id')
                ->toArray();
        };

        return [
            'bp'  => array_filter($classify(BalanceSheet::class, 'bp')),
            'dre' => array_filter($classify(IncomeStatement::class, 'dre')),
        ];

    }

    public function getClassificationDesc(int $idFile): array
    {
        $aClassify = $this->getClassification($idFile);

        $classify = function (string $modelClass, string $type) use ($aClassify): array {

            if (empty($aClassify[$type]) || !is_array($aClassify[$type])) {
                return [];
            }

            $ordered = $aClassify[$type];

            uksort($ordered, fn ($a, $b) =>
                strlen((string) $b) <=> strlen((string) $a)
                    ?: strcmp((string) $b, (string) $a)
            );

            $ids = array_values($ordered);

            $ids = array_values(array_unique($ids));

            if (empty($ids)) return [];

            $models = $modelClass::query()
                ->whereIn('id', $ids)
                ->get()
                ->sortBy(fn ($model) => array_search($model->id, $ids, true))
                ->pluck('value', 'id')
                ->toArray();

            return $models;
        };

        return [
            'bp'  => $classify(PivotBalanceSheetReference::class, 'bp'),
            'dre' => $classify(PivotIncomeStatementReference::class, 'dre'),
        ];
    }

}
