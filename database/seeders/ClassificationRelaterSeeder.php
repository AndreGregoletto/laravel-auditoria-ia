<?php

namespace Database\Seeders;

use App\Models\IncomeStatement;
use App\Models\PivotBalanceSheetReference;
use App\Models\PivotIncomeStatementReference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassificationRelaterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        /**
         * =========================================================
         * BP (balance sheet)
         * =========================================================
         */
        $balanceSheets = [
            ['balance' => 1,  'value'  => '1.1.0.1'],
            ['balance' => 2,  'value'  => '1.1.0.3'],
            ['balance' => 3,  'value'  => '1.1.0.11.02026'],
            ['balance' => 4,  'value'  => ''],
            ['balance' => 5,  'value'  => '1.1.0.7'],
            ['balance' => 6,  'value'  => '1.1.0.51.03'],
            ['balance' => 7,  'value'  => '1.1.1.91.'],
            ['balance' => 8,  'value'  => '1.1.1.22'],
            ['balance' => 9,  'value'  => ''],
            ['balance' => 10, 'value'  => ''],
            ['balance' => 11, 'value'  => ''],
            ['balance' => 12, 'value'  => '1.2.0.5'],
            ['balance' => 13, 'value'  => ''],
            ['balance' => 14, 'value'  => '1.2.0.61'],
            ['balance' => 15, 'value'  => '1.2.1.91.'],
            ['balance' => 16, 'value'  => ''],
            ['balance' => 17, 'value'  => ''],
            ['balance' => 18, 'value'  => ''],
            ['balance' => 19, 'value'  => '1.2.3.21'],
            ['balance' => 20, 'value'  => ''],
            ['balance' => 21, 'value'  => '2.1.0.1'],
            ['balance' => 22, 'value'  => '2.1.0.31'],
            ['balance' => 23, 'value'  => '2.1.0.5'],
            ['balance' => 24, 'value'  => ''],
            ['balance' => 25, 'value'  => '2.1.0.21'],
            ['balance' => 26, 'value'  => ''],
            ['balance' => 27, 'value'  => '2.1.0.'], #GG aparece com 71 e 86
            ['balance' => 28, 'value'  => ''],
            ['balance' => 29, 'value'  => ''],
            ['balance' => 30, 'value'  => ''],
            ['balance' => 31, 'value'  => ''],
            ['balance' => 32, 'value'  => ''],
            ['balance' => 33, 'value'  => ''],
            ['balance' => 34, 'value'  => '2.2.0.21.'],
            ['balance' => 35, 'value'  => ''],
            ['balance' => 36, 'value'  => ''],
            ['balance' => 37, 'value'  => '2.2.0.62'],
            ['balance' => 38, 'value'  => ''],
            ['balance' => 39, 'value'  => ''],
            ['balance' => 40, 'value'  => '2.2.0.25'],
            ['balance' => 41, 'value'  => ''],
            ['balance' => 42, 'value'  => '2.2.1.95'],
            ['balance' => 43, 'value'  => '2.4.0.11'],
            ['balance' => 44, 'value'  => ''],
            ['balance' => 45, 'value'  => ''],
            ['balance' => 46, 'value'  => '2.4.0.'],
            ['balance' => 47, 'value'  => 'DRE'],

            #excptions
            // ['balance' => 6, 'value'  => '1.1.0.11.2006'],
        ];

        foreach ($balanceSheets as $row) {
            PivotBalanceSheetReference::insert(
                [
                    'balance_sheet_id' => $row['balance'],
                    'value'            => $row['value'],
                    'company_tree_id'  => null,
                    'company_id'       => null,
                    'status'           => 1,
                    'create_user_id'   => 1,
                    'alter_user_id'    => 1,
                ]
            );
        }


        /**
         * =========================================================
         * DRE (income_statements)
         * =========================================================
         */
        $incomeStatements = [
            ['income' => 1,  'value'  => '6.1.0.11.0'],
            ['income' => 2,  'value'  => '6.1.0.11.3'],
            ['income' => 3,  'value'  => '6.1.0.51.'],
            ['income' => 4,  'value'  => '6.1.0.54.'],
            ['income' => 5,  'value'  => '6.1.1.11.'],
            ['income' => 6,  'value'  => ''],
            ['income' => 7,  'value'  => '6.3.0.14'],
            ['income' => 8,  'value'  => '6.3.0.54.'],
            ['income' => 9,  'value'  => '7.5.0.11.'],
            ['income' => 10, 'value'  => ''],
            ['income' => 11, 'value'  => ''],
        ];

        foreach ($incomeStatements as $row) {
            PivotIncomeStatementReference::insert(
                [
                    'income_statement_id' => $row['income'],
                    'value'               => $row['value'],
                    'company_tree_id'     => null,
                    'company_id'          => null,
                    'status'              => 1,
                    'create_user_id'      => 1,
                    'alter_user_id'       => 1,
                ]
            );
        }
    }
}
