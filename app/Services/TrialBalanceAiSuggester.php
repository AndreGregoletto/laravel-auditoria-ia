<?php

namespace App\Services;

use App\Models\Queue\TrialBalanceData;

class TrialBalanceAiSuggester
{
    public function suggestForFile(int $fileId): array
    {
        $rows = TrialBalanceData::query()
            ->where('file_id', $fileId)
            ->orderBy('file_line')
            ->get();

        $out = [];
        $max = 0;
        foreach ($rows as $r) {
            $included = false;
            $redflag  = 0.0;
            $eps      = 0.05;
            $debit    = (float) $r->debit;
            $credit   = (float) $r->credit;
            $monthly  = (float) $r->monthly_activity;
            $previous = (float) $r->previous_balance;
            $closing  = (float) $r->closing_balance;

            if (abs(($debit - $credit) - $monthly) > $eps) {
                $redflag += 0.33;
            }

            if (abs(($previous + $monthly) - $closing) > $eps) {
                $redflag += 0.33;
            }

            if (blank($r->account) || blank($r->description)) {
                $redflag += 0.34;
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
                $base = $included ? 70 : 55;
                $confidence = (int) max(0, min(100, round($base - ($redflag * 40))));
            }

            if ($len === null) {
                $rationale = 'Sugestão automática baseada em validações contábeis básicas (sem regra de nível aplicável).';
            } else {
                $rationale = $included
                    ? "Sugestão baseada em padrão do plano: último nível com {$len} caracteres indica conta analítica."
                    : "Sugestão baseada em padrão do plano: último nível com {$len} caracteres indica conta sintética/controle.";
            }

            $out[$r->id] = [
                'included'   => $included,
                'confidence' => $confidence,
                'rationale'  => $rationale,
                'redflag'    => $redflag,
                'closing'    => $closing,
            ];
        }

        if($max >= 1){
            $min = $max - 0.05;
            $max = $max + 0.05;
            foreach ($out as $key => $o){
                if($o['closing'] >= $min && $o['closing'] <= $max && $o['included']){
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

}
