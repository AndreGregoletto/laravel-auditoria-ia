<?php

namespace App\Services;

use App\Models\ImportFile;
use App\Models\PivotBalanceSheetReference;
use App\Models\PivotIncomeStatementReference;
use App\Models\Queue\TrialBalanceData;

class TrialBalanceAiSuggester
{
    public function suggestForFile(int $fileId): array
    {
        $classification = $this->getClassification($fileId);
        dd($classification);

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

            if($included){
                $fourNumber = substr($r->account, 0, 4);
                $aBp        = ['1.1.', '1.2.', '2.2.', '2.4.'];

                if(in_array($fourNumber, $aBp)){
//                   $balance_sheet = match ($)
                }
            }
            /*
             * Classification BP & DRE
             */

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
            $value = match ($type){
                'bp'  => 'balance_sheet_id',
                'dre' => 'income_statement_id',
            };

            $result = $modelClass::where('company_id', $companyId)->get()->pluck('value', $value);
            if ($result->isNotEmpty()) return $result->toArray();

            $result = $modelClass::where('company_tree_id', $companyId)->get()->pluck('value', $value);
            if ($result->isNotEmpty()) return $result->toArray();

            return $modelClass::whereNull('company_id')->whereNull('company_tree_id')->get()->pluck('value', $value)->toArray();
        };

        return [
            'bp'  => array_filter($priority(PivotBalanceSheetReference::class, 'bp')),
            'dre' => array_filter($priority(PivotIncomeStatementReference::class, 'dre')),
        ];

    }

}
