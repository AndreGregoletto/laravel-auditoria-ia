<?php

namespace App\Services;

use App\Models\BalanceSheet;
use App\Models\Company;
use App\Models\ImportFile;
use App\Models\IncomeStatement;
use App\Models\PivotIncomeStatementReference;
use App\Models\Queue\TrialBalanceData;
use Illuminate\Support\Collection;

class GenerateRAG
{
    public Collection $importFile;
    public array $fileOrder = [];

    public function getFiles($aFiles): void
    {
        $this->importFile = ImportFile::whereIn('id', $aFiles)
            ->where('file_step_id', 2)
            ->where('file_status_id', 3)
            ->orderBy('reference_year', 'ASC')
            ->orderBy('reference_month', 'ASC')
            ->get();
    }

    public function getDataFile($id): Collection
    {
        return TrialBalanceData::query()
            ->where('file_id', $id)
            ->where('status', 1)
            ->get();
    }

    public function validatorFiles(array $aFiles): bool
    {
        $this->getFiles($aFiles);
        $files     = (array) [];
        $queue     = $this->importFile;
        $idCompany = (int) 0;

        if ($this->importFile->count() !== count($aFiles)) return false;

        foreach ($queue as $q) {
            if ($q->company_id !== $idCompany && $idCompany !== 0) return false;

            $files[$q->id] = [
                'company_id'      => $q->company_id,
                'reference_month' => $q->reference_month,
                'reference_year'  => $q->reference_year,
            ];

            $idCompany = $q->company_id;
        }

        if (count($files) < 2) return false;

        $this->fileOrder = $files;

        return true;
    }

    public function startProcess(array $aFiles, bool $download): array
    {
        try {
            if (!$this->validatorFiles($aFiles)) {
                throw new \Exception(__('error.invalid_file'));
            }

            $idCompany = (int) reset($this->fileOrder)['company_id'];
            $company   = Company::find($idCompany);

            $response  = (array) [];
            $aClosing  = (array) [];
            $iClosing  = (float) 0.0;
            $idFile    = (int) null;
            $file      = null;

            $ragName   = (string) "RAG - {$company->name} | " . __('files.period') . ":";

            $startFile = reset($this->fileOrder);
            $endFile   = end($this->fileOrder);

            $startPeriodStr = sprintf('%02d/%d', $startFile['reference_month'], $startFile['reference_year']);
            $endPeriodStr   = sprintf('%02d/%d', $endFile['reference_month'], $endFile['reference_year']);
            $periodRange    = "{$startPeriodStr} a {$endPeriodStr}";

            $initialPeriod = "{$startFile['reference_month']}/{$startFile['reference_year']}";
            $finalPeriod   = "{$endFile['reference_month']}/{$endFile['reference_year']}";

            $ragName .= "{$endFile['reference_month']}/{$endFile['reference_year']}";

            $bpCatalog = $this->getBalanceSheetCatalogForCompany($idCompany);

            $trialBalance = TrialBalanceData::query()
                ->whereIn('file_id', array_keys($this->fileOrder))
                ->where('balance_included', 1)
                ->where('status', 1)
                ->orderBy('file_id')
                ->orderBy('file_line')
                ->get([
                    'account', 'description', 'closing_balance', 'file_id',
                    'balance_last_decision_id', 'balance_decision_source',
                    'balance_sheet_id', 'income_statement_id',
                ]);

            $aBp = (array) [
                'assets' => [
                    $initialPeriod => ['sum' => 0.0],
                    $finalPeriod   => ['sum' => 0.0],
                ],
                'liabilities' => [
                    $initialPeriod => ['sum' => 0.0],
                    $finalPeriod   => ['sum' => 0.0],
                ],
                'currentAssets' => [
                    $initialPeriod => ['sum' => 0.0],
                    $finalPeriod   => ['sum' => 0.0],
                ],
                'nomCurrentAssets' => [
                    $initialPeriod => ['sum' => 0.0],
                    $finalPeriod   => ['sum' => 0.0],
                ],
                'currentLiabilities' => [
                    $initialPeriod => ['sum' => 0.0],
                    $finalPeriod   => ['sum' => 0.0],
                ],
                'nomCurrentLiabilities' => [
                    $initialPeriod => ['sum' => 0.0],
                    $finalPeriod   => ['sum' => 0.0],
                ],
                'freeHeritage' => [
                    $initialPeriod => ['sum' => 0.0],
                    $finalPeriod   => ['sum' => 0.0],
                ],
                'bpAll' => [
                    $initialPeriod => ['sum' => 0.0],
                    $finalPeriod   => ['sum' => 0.0],
                ],
            ];

            $dreCatalog = $this->getIncomeStatementCatalogForCompany($idCompany);

            $aDre = [
                'catalog' => $dreCatalog,
                'codes'   => [
                    $initialPeriod => [],
                    $finalPeriod   => [],
                ],
                'result'  => [
                    $initialPeriod => 0.0,
                    $finalPeriod   => 0.0,
                ],
            ];

            $bsCache = [];

            foreach ($trialBalance as $tb) {

                if ($idFile !== $tb->file_id) {
                    $idFile = (int) $tb->file_id;
                    $file   = ImportFile::where('id', $idFile)->first();

                    $iClosing = (float) TrialBalanceData::query()
                        ->where('file_id', $idFile)
                        ->where('balance_included', 1)
                        ->sum('closing_balance');
                }

                $ref            = "{$file->reference_month}/{$file->reference_year}";
                $tb->file_id    = $file;
                $aClosing[$ref] = $iClosing;

                $account = trim((string) $tb->account);

                if (!isset($response[$account])) {
                    $response[$account] = [
                        'clear_account' => (string) str_replace('.', '', $account),
                        'description'   => (string) $tb->description,
                        'balance'       => (array) [],
                    ];
                }

                $response[$account]['balance'][$ref] = (float) ($response[$account]['balance'][$ref] ?? 0.0)
                    + (float) $tb->closing_balance;

                if ($download && in_array($ref, [$initialPeriod, $finalPeriod], true)) {

                    $balance = (float) $tb->closing_balance;

                    $bsId = $tb->balance_sheet_id ?? null;
                    if (empty($bsId)) continue;

                    if (!isset($bsCache[$bsId])) {
                        $bsCache[$bsId] = BalanceSheet::query()->find($bsId);
                    }
                    $bs = $bsCache[$bsId];
                    if (!$bs) continue;

                    $classCode = (string) $bs->code;
                    $bucket    = null;

                    if ($bs->side === 'assets') {
                        $bucket = ($bs->section === 'current') ? 'currentAssets' : 'nomCurrentAssets';
                        $aBp['assets'][$ref]['sum'] = (float) ($aBp['assets'][$ref]['sum'] ?? 0.0) + $balance;

                    } elseif ($bs->side === 'liabilities') {
                        $bucket = ($bs->section === 'current') ? 'currentLiabilities' : 'nomCurrentLiabilities';
                        $aBp['liabilities'][$ref]['sum'] = (float) ($aBp['liabilities'][$ref]['sum'] ?? 0.0) + $balance;

                    } elseif ($bs->side === 'equity') {
                        $bucket = 'freeHeritage';
                        $aBp['liabilities'][$ref]['sum'] = (float) ($aBp['liabilities'][$ref]['sum'] ?? 0.0) + $balance;
                    }

                    if (!$bucket) continue;

                    $aBp[$bucket][$ref]['sum']      = (float) ($aBp[$bucket][$ref]['sum'] ?? 0.0) + $balance;
                    $aBp[$bucket][$ref][$classCode] = (float) ($aBp[$bucket][$ref][$classCode] ?? 0.0) + $balance;

                    $aBp['bpAll'][$ref]['sum'] = (float) ($aBp['bpAll'][$ref]['sum'] ?? 0.0) + $balance;
                }
            }

            if ($download) {
                foreach ([$initialPeriod, $finalPeriod] as $ref) {
                    $bpSum     = (float) ($aBp['bpAll'][$ref]['sum'] ?? 0.0);
                    $dreResult = -1.0 * $bpSum; #revisar

                    $aDre['result'][$ref] = $dreResult;

                    $aBp['freeHeritage'][$ref]['sum'] = (float) ($aBp['freeHeritage'][$ref]['sum'] ?? 0.0) + $dreResult;
                    $aBp['freeHeritage'][$ref]['DRE'] = (float) ($aBp['freeHeritage'][$ref]['DRE'] ?? 0.0) + $dreResult;
                    $aBp['liabilities'][$ref]['sum'] = (float) ($aBp['liabilities'][$ref]['sum'] ?? 0.0) + $dreResult;
                }
            }

            return [
                'name'        => $ragName,
                'companyName' => $company->name,
                'periodRange' => $periodRange,
                'aClosing'    => $aClosing,
                'response'    => $response,
                'fileOrder'   => $this->fileOrder,
                'BP'          => $aBp,
                'BP_CATALOG'  => $bpCatalog,
                'DRE'         => $aDre,
            ];

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    private function getBalanceSheetCatalogForCompany(int $companyId): array
    {
        $companyTreeId = Company::where('id', $companyId)->value('company_tree_id');

        $rows = BalanceSheet::query()->where('company_id', $companyId)->get();

        if ($rows->isEmpty() && !empty($companyTreeId)) {
            $rows = BalanceSheet::query()->where('company_tree_id', $companyTreeId)->get();
        }

        if ($rows->isEmpty()) {
            $rows = BalanceSheet::query()
                ->whereNull('company_id')
                ->whereNull('company_tree_id')
                ->get();
        }

        $rows = $rows->sortBy(function ($r) {
            return [(int)($r->sort_order ?? 999999), (string)$r->code];
        });

        $out = [
            'currentAssets'         => [],
            'nomCurrentAssets'      => [],
            'currentLiabilities'    => [],
            'nomCurrentLiabilities' => [],
            'freeHeritage'          => [],
        ];

        foreach ($rows as $bs) {
            $bucket = null;

            if ($bs->side === 'assets') {
                $bucket = ($bs->section === 'current') ? 'currentAssets' : 'nomCurrentAssets';
            } elseif ($bs->side === 'liabilities') {
                $bucket = ($bs->section === 'current') ? 'currentLiabilities' : 'nomCurrentLiabilities';
            } elseif ($bs->side === 'equity') {
                $bucket = 'freeHeritage';
            }

            if (!$bucket) continue;

            $out[$bucket][(string)$bs->code] = (string)$bs->name;
        }

        return $out;
    }

    private function getIncomeStatementCatalogForCompany(int $companyId): array
    {
        $companyTreeId = Company::where('id', $companyId)->value('company_tree_id');

        $rows = PivotIncomeStatementReference::query()->where('company_id', $companyId)->get();

        if ($rows->isEmpty() && !empty($companyTreeId)) {
            $rows = PivotIncomeStatementReference::query()->where('company_tree_id', $companyTreeId)->get();
        }

        if ($rows->isEmpty()) {
            $rows = PivotIncomeStatementReference::query()
                ->whereNull('company_id')
                ->whereNull('company_tree_id')
                ->get();
        }

        $rows = $rows->sortBy(function ($r) {
            return [(int)($r->sort_order ?? 999999), (string)$r->code];
        });

        $out = [];
        foreach ($rows as $is) {
            $out[(string) $is->code] = (string) $is->name;
        }

        return $out;
    }
}
