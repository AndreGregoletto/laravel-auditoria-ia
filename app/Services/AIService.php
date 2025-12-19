<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Maps columns from a balance sheet based on the header and sample
     * ALWAYS returns numeric indices
     */
    public function mapTrialBalanceColumns(array $headers, array $sample): array
    {
        Log::info('IA analisando colunas do balancete', [
            'headers' => $headers,
            'sample'  => $sample,
        ]);

        $normalized = array_map(fn ($h) => mb_strtolower(trim($h)), $headers);

        $map = [
            'account'          => null,
            'description'      => null,
            'previous_balance' => null,
            'debit'            => null,
            'credit'           => null,
            'monthly_activity' => null,
            'closing_balance'  => null,
        ];

        foreach ($normalized as $index => $column) {
            $column = strtolower($column);
            $column = mb_strtolower($column);
            $column = trim(preg_replace('/\s+/', ' ', $column));

            if (
                str_contains($column, 'conta')
            ) {
                $map['account'] = $index;
            }

            if (str_contains($column, 'descri')) {
                $map['description'] = $index;
            }

            if (
                str_contains($column, 'saldo anterior') ||
                str_contains($column, 'anterior')
            ) {
                $map['previous_balance'] = $index;
            }

            if (
                str_contains($column, 'débito') ||
                str_contains($column, 'debito')
            ) {
                $map['debit'] = $index;
            }

            if (
                str_contains($column, 'crédito') ||
                str_contains($column, 'credito')
            ) {
                $map['credit'] = $index;
            }

            if (
                str_contains($column, 'mov periodo') ||
                str_contains($column, 'período') ||
                str_contains($column, 'periodo')
            ) {
                $map['monthly_activity'] = $index;
            }

            if (
                str_contains($column, 'saldo atual') ||
                str_contains($column, 'atual') ||
                str_contains($column, 'final')
            ) {
                $map['closing_balance'] = $index;
            }
        }
//        dd($map);

        Log::info('Mapeamento final de colunas', $map);
        return $map;
    }
}
