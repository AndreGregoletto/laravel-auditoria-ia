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
            'account'              => null,
            'description'          => null,
            'previous_balance'     => null,
            'month_balance'        => null,
            'current_balance'      => null,
            'percentage_variation' => null,
        ];

        foreach ($normalized as $index => $column) {

            if (str_contains($column, 'conta')) {
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
                str_contains($column, 'movimento') ||
                str_contains($column, 'mês') ||
                str_contains($column, 'mes')
            ) {
                $map['month_balance'] = $index;
            }

            if (
                str_contains($column, 'saldo atual') ||
                str_contains($column, 'atual')
            ) {
                $map['current_balance'] = $index;
            }

            if (
                str_contains($column, '%') ||
                str_contains($column, 'variação')
            ) {
                $map['percentage_variation'] = $index;
            }
        }

        Log::info('Mapeamento final de colunas', $map);

        return $map;
    }
}
