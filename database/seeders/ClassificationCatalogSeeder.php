<?php

namespace Database\Seeders;

use App\Models\BalanceSheet;
use App\Models\IncomeStatement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassificationCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /**
         * =========================================================
         * BP (balance_sheets) — Catálogo global default
         * side: ASSET | LIABILITY | EQUITY
         * section: CURRENT | NON_CURRENT | EQUITY
         * parent_code: hierarquia (ex: C.1 parent C)
         * sort_order: ordem fixa do relatório
         * =========================================================
         */
        $balanceSheets = [
            // ASSET - CURRENT
            ['code' => 'A',   'name' => 'Caixa e equivalentes de caixa',              'config_name' => 'cash_and_cash',       'side' => 'assets', 'section' => 'current', 'parent_code' => null, 'sort_order' => 10],
            ['code' => 'B',   'name' => 'Contas a receber',                           'config_name' => 'accounts_receivable', 'side' => 'assets', 'section' => 'current', 'parent_code' => null, 'sort_order' => 20],
            ['code' => 'C',   'name' => 'Depósitos vinculados - conta reserva (CP)',  'config_name' => 'reserve_account_cp',  'side' => 'assets', 'section' => 'current', 'parent_code' => null, 'sort_order' => 30],
            ['code' => 'D',   'name' => 'Arrendamento financeiro a receber (CP)',     'config_name' => 'financial_lease_rec', 'side' => 'assets', 'section' => 'current', 'parent_code' => null, 'sort_order' => 40],
            ['code' => 'E',   'name' => 'Estoques',                                   'config_name' => 'stocks',              'side' => 'assets', 'section' => 'current', 'parent_code' => null, 'sort_order' => 50],
            ['code' => 'F',   'name' => 'Tributos a recuperar (CP)',                  'config_name' => 'taxes_recoverable',   'side' => 'assets', 'section' => 'current', 'parent_code' => null, 'sort_order' => 60],
            ['code' => 'G',   'name' => 'Adiantamento a fornecedores',                'config_name' => 'advance_payments_to', 'side' => 'assets', 'section' => 'current', 'parent_code' => null, 'sort_order' => 70],
            ['code' => 'H',   'name' => 'Despesas antecipadas',                       'config_name' => 'prepaid_expenses',    'side' => 'assets', 'section' => 'current', 'parent_code' => null, 'sort_order' => 80],
            ['code' => 'I',   'name' => 'Outros créditos (CP)',                       'config_name' => 'other_credits_cp',    'side' => 'assets', 'section' => 'current', 'parent_code' => null, 'sort_order' => 90],

            // ASSET - NON_CURRENT
            ['code' => 'C.1', 'name' => 'Depósitos vinculados - conta reserva (LP)',  'config_name' => 'linked_deposits_lp',    'side' => 'assets', 'section' => 'non_current', 'parent_code' => 'C',  'sort_order' => 110],
            ['code' => 'D.1', 'name' => 'Arrendamento financeiro a receber (LP)',     'config_name' => 'financial_lease_lp',    'side' => 'assets', 'section' => 'non_current', 'parent_code' => 'D',  'sort_order' => 120],
            ['code' => 'F.1', 'name' => 'Tributos a recuperar (LP)',                  'config_name' => 'taxes_to_be_recovered', 'side' => 'assets', 'section' => 'non_current', 'parent_code' => 'F',  'sort_order' => 130],
            ['code' => 'I.1', 'name' => 'Outros créditos (LP)',                       'config_name' => 'other_credits',         'side' => 'assets', 'section' => 'non_current', 'parent_code' => 'I',  'sort_order' => 140],
            ['code' => 'J',   'name' => 'Depósitos judiciais',                        'config_name' => 'judicial_deposits',     'side' => 'assets', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 150],
            ['code' => 'K',   'name' => 'Partes relacionadas (LP)',                   'config_name' => 'related_parties',       'side' => 'assets', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 160],
            ['code' => 'L',   'name' => 'Investimento',                               'config_name' => 'investment',            'side' => 'assets', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 170],
            ['code' => 'M',   'name' => 'Propriedade para investimento',              'config_name' => 'investment_property',   'side' => 'assets', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 180],
            ['code' => 'M.1', 'name' => 'Direito de uso - arrendamento mercantil',    'config_name' => 'use_lease',             'side' => 'assets', 'section' => 'non_current', 'parent_code' => 'M',  'sort_order' => 190],
            ['code' => 'N',   'name' => 'Imobilizado',                                'config_name' => 'fixed_assets',          'side' => 'assets', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 200],
            ['code' => 'O',   'name' => 'Intangível',                                 'config_name' => 'intangible',            'side' => 'assets', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 210],

            // LIABILITY - CURRENT
            ['code' => 'AA',  'name' => 'Fornecedores (CP)',                      'config_name' => 'suppliers_cp',                 'side' => 'liabilities', 'section' => 'current', 'parent_code' => null, 'sort_order' => 310],
            ['code' => 'BB',  'name' => 'Obrigações sociais e trabalhistas',      'config_name' => 'social_and_labor_obligations', 'side' => 'liabilities', 'section' => 'current', 'parent_code' => null, 'sort_order' => 320],
            ['code' => 'CC',  'name' => 'Obrigações tributárias (CP)',            'config_name' => 'tax_obligations',              'side' => 'liabilities', 'section' => 'current', 'parent_code' => null, 'sort_order' => 330],
            ['code' => 'DD',  'name' => 'Arrendamentos financeiros a pagar (CP)', 'config_name' => 'financial_leases_payable',     'side' => 'liabilities', 'section' => 'current', 'parent_code' => null, 'sort_order' => 340],
            ['code' => 'EE',  'name' => 'Empréstimos e financiamentos (CP)',      'config_name' => 'loans_and_financing',          'side' => 'liabilities', 'section' => 'current', 'parent_code' => null, 'sort_order' => 350],
            ['code' => 'FF',  'name' => 'Debêntures (CP)',                        'config_name' => 'debentures',                   'side' => 'liabilities', 'section' => 'current', 'parent_code' => null, 'sort_order' => 360],
            ['code' => 'GG',  'name' => 'Dividendos (CP)',                        'config_name' => 'dividends',                    'side' => 'liabilities', 'section' => 'current', 'parent_code' => null, 'sort_order' => 370],
            ['code' => 'HH',  'name' => 'Pesquisas e desenvolmentos - P&D (CP)',  'config_name' => 'research_and_development',     'side' => 'liabilities', 'section' => 'current', 'parent_code' => null, 'sort_order' => 380],
            ['code' => 'II',  'name' => 'Outras obrigações (CP)',                 'config_name' => 'other_obligations',            'side' => 'liabilities', 'section' => 'current', 'parent_code' => null, 'sort_order' => 390],

            // LIABILITY - NON_CURRENT
            ['code' => 'AA.1','name' => 'Fornecedores (LP)',                       'config_name' => 'suppliers_lp',                 'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => 'AA', 'sort_order' => 410],
            ['code' => 'CC.1','name' => 'Obrigações tributárias (LP)',             'config_name' => 'tax_obligations_lp',           'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => 'CC', 'sort_order' => 420],
            ['code' => 'JJ',  'name' => 'Tributos diferidos',                      'config_name' => 'deferred_taxes',               'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 430],
            ['code' => 'DD.1','name' => 'Arrendamentos financeiros a pagar (LP)',  'config_name' => 'final_leases_payable',         'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => 'DD', 'sort_order' => 440],
            ['code' => 'EE.1','name' => 'Empréstimos e financiamentos (LP)',       'config_name' => 'loans_and_financing_lp',       'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => 'EE', 'sort_order' => 450],
            ['code' => 'FF.1','name' => 'Debêntures (LP)',                         'config_name' => 'debentures_lp',                'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => 'FF', 'sort_order' => 460],
            ['code' => 'KK',  'name' => 'Provisão para perdas de investimentos',   'config_name' => 'provision_for_losses',         'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 470],
            ['code' => 'LL',  'name' => 'Passivos contingentes',                   'config_name' => 'contingent_liabilities',       'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 480],
            ['code' => 'MM',  'name' => 'Adiantamento de clientes',                'config_name' => 'customer_advances',            'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 490],
            ['code' => 'NN',  'name' => 'Provisão para desmobilização dos ativos', 'config_name' => 'provision_for_asset_disposal', 'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 500],
            ['code' => 'OO',  'name' => 'Partes relacionadas',                     'config_name' => 'related_parties_lp',           'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => null, 'sort_order' => 510],
            ['code' => 'HH.1','name' => 'Pesquisas e desenvolmentos - P&D (LP)',   'config_name' => 'research_and_development_lp',  'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => 'HH', 'sort_order' => 520],
            ['code' => 'II.1','name' => 'Outras obrigações (LP)',                  'config_name' => 'other_liabilities_lp',         'side' => 'liabilities', 'section' => 'non_current', 'parent_code' => 'II', 'sort_order' => 530],

            // EQUITY
            ['code' => 'XX',  'name' => 'Capital social',                         'config_name' => 'share_capital',              'side' => 'equity', 'section' => 'equity', 'parent_code' => null, 'sort_order' => 610],
            ['code' => 'XX.1','name' => 'Reserva de capital',                     'config_name' => 'capital_reserve',            'side' => 'equity', 'section' => 'equity', 'parent_code' => 'XX', 'sort_order' => 620],
            ['code' => 'XX.2','name' => 'Ajuste de avaliação patrimonial',        'config_name' => 'asset_valuation_adjustment', 'side' => 'equity', 'section' => 'equity', 'parent_code' => 'XX', 'sort_order' => 630],
            ['code' => 'XX.3','name' => 'Prejuízo acumulado',                     'config_name' => 'accumulated_loss',           'side' => 'equity', 'section' => 'equity', 'parent_code' => 'XX', 'sort_order' => 640],
            ['code' => 'DRE', 'name' => 'Demonstração do resultado do exercício', 'config_name' => 'statement_of_income',        'side' => 'equity', 'section' => 'equity', 'parent_code' => null, 'sort_order' => 650],
        ];

        foreach ($balanceSheets as $row) {
//            dd($row);
            BalanceSheet::insert(
                [
                    'code'            => $row['code'],
                    'company_tree_id' => null,
                    'company_id'      => null,
                    'name'        => $row['name'],
                    'config_name' => $row['config_name'],
                    'parent_code' => $row['parent_code'],
                    'sort_order'  => $row['sort_order'],
                    'side'        => $row['side'],
                    'section'     => $row['section'],
                    'status'      => 1,
                    'updated_at'  => $now,
                    'created_at'  => $now,
                ]
            );
        }

        /**
         * =========================================================
         * DRE (income_statements) — Catálogo global default
         * =========================================================
         */
        $incomeStatements = [
            ['code' => '10',   'name' => 'Receita bruta',                                     'config_name' => 'gross_revenue',                       'parent_code' => null, 'sort_order' => 10],
            ['code' => '10.1', 'name' => '(-) Deduções da receita',                           'config_name' => 'income_deductions',                   'parent_code' => '10', 'sort_order' => 20],
            ['code' => '20',   'name' => 'Custo',                                             'config_name' => 'cost',                                'parent_code' => null, 'sort_order' => 30],
            ['code' => '30',   'name' => 'Gerais e administrativas',                          'config_name' => 'general_and_administrative',          'parent_code' => null, 'sort_order' => 40],
            ['code' => '40',   'name' => 'Outras receitas (despesas) operacionais, líquidas', 'config_name' => 'other_operating_income',              'parent_code' => null, 'sort_order' => 50],
            ['code' => '50',   'name' => 'Resultado de equivalência patrimonial',             'config_name' => 'equity_method_result',                'parent_code' => null, 'sort_order' => 60],
            ['code' => '60',   'name' => 'Receita financeira',                                'config_name' => 'financial_income',                    'parent_code' => null, 'sort_order' => 70],
            ['code' => '70',   'name' => 'Despesa financeira',                                'config_name' => 'financial_expense',                   'parent_code' => null, 'sort_order' => 80],
            ['code' => '80',   'name' => 'Imposto de renda e contribuição social corrente',   'config_name' => 'current_income_social_contribution',  'parent_code' => null, 'sort_order' => 90],
            ['code' => '90',   'name' => 'Imposto de renda e contribuição social diferido',   'config_name' => 'deferred_income_social_contribution', 'parent_code' => null, 'sort_order' => 100],
            ['code' => '100',  'name' => 'Incentivos fiscais (sudene)',                       'config_name' => 'tax_incentives',                      'parent_code' => null, 'sort_order' => 110],
        ];

        foreach ($incomeStatements as $row) {
            IncomeStatement::insert(
                [
                    'code'            => $row['code'],
                    'company_tree_id' => null,
                    'company_id'      => null,
                    'name'        => $row['name'],
                    'config_name' => $row['config_name'],
                    'parent_code' => $row['parent_code'],
                    'sort_order'  => $row['sort_order'],
                    'status'      => 1,
                    'updated_at'  => $now,
                    'created_at'  => $now,
                ]
            );
        }
    }
}
