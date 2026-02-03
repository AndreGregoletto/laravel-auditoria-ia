<?php

namespace App\Services;

use App\Models\Company;
use App\Models\ImportFile;
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

        foreach ($queue as $q){
            if($q->company_id !== $idCompany && $idCompany !== 0) return false;

            $files[$q->id] = [
                'company_id'      => $q->company_id,
                'reference_month' => $q->reference_month,
                'reference_year'  => $q->reference_year,
            ];

            $idCompany = $q->company_id;
        }

        if(count($files) < 2) return false;

        $this->fileOrder = $files;

        return true;
    }

    public function startProcess(array $aFiles, bool $download): array
    {
        try {
            if(!$this->validatorFiles($aFiles)){
                throw new \Exception(__('error.invalid_file'));
            }

            $idCompany = reset($this->fileOrder)['company_id'];
            $company   = Company::find($idCompany);
            $response  = (array) [];
            $aClosing  = (array) [];
            $iClosing  = (int) 0;
            $idFile    = (int) null;
            $file      = null;
            $ragName   = (string) "RAG - {$company->name} | " . __('files.period') . ":";
            $startFile = reset($this->fileOrder);
            $endFile   = end($this->fileOrder);
            $startFile = reset($this->fileOrder);
            $endFile   = end($this->fileOrder);

            $startPeriodStr = sprintf('%02d/%d', $startFile['reference_month'], $startFile['reference_year']);
            $endPeriodStr   = sprintf('%02d/%d', $endFile['reference_month'], $endFile['reference_year']);

            $periodRange = "{$startPeriodStr} a {$endPeriodStr}";

            $initialPeriod = "{$startFile['reference_month']}/{$startFile['reference_year']}";
            $finalPeriod   = "{$endFile['reference_month']}/{$endFile['reference_year']}";
            $ragName      .= "{$endFile['reference_month']}/{$endFile['reference_year']}";

            $trialBalance = TrialBalanceData::query()
                ->whereIn('file_id', array_keys($this->fileOrder))
                ->where('balance_included', 1)
                ->where('status', 1)
                ->orderBy('file_id')
                ->orderBy('file_line')
                ->get([
                    'account', 'description', 'closing_balance', 'file_id',
                    'balance_last_decision_id', 'balance_decision_source'
                ]);

            $aDre = (array) [];
            $aBp  = (array) [
                'assets' => [
                    $initialPeriod => [ 'sum' => 0.0 ],
                    $finalPeriod   => [ 'sum' => 0.0 ],
                ],
                'currentAssets'         => [],
                'nomCurrentAssets'      => [],
                'liabilities'           => [
                    $initialPeriod => [ 'sum' => 0.0 ],
                    $finalPeriod   => [ 'sum' => 0.0 ],
                ],
                'currentLiabilities'    => [],
                'nomCurrentLiabilities' => [],
                'freeHeritage'          => [],
            ];

            foreach ($trialBalance as $tb){
                /** Start Process Balances */

                if($idFile !== $tb->file_id){
                    $idFile = $tb->file_id;
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
                $response[$account]['balance'][$ref] = (float) ($response[$account]['balance'][$ref] ?? 0.0) + (float) $tb->closing_balance;

                if ($download) {
                    if (in_array($ref, [$initialPeriod, $finalPeriod])) {
                        $fourNumber = substr($tb->account, 0, 4);
                        $balance    = (float) $tb->closing_balance;

                        $addToSection = function($section) use (&$aBp, $ref, $account, $balance) {
                            if (!isset($aBp[$section][$ref]['sum'])) {
                                $aBp[$section][$ref]['sum'] = 0.0;
                            }
                            if (!isset($aBp[$section][$ref][$account])) {
                                $aBp[$section][$ref][$account] = 0.0;
                            }

                            $aBp[$section][$ref][$account] += $balance;
                            $aBp[$section][$ref]['sum']    += $balance;
                        };

                        switch ($fourNumber) {
                            case '1.1.':
                                $addToSection('currentAssets');
                                $aBp['assets'][$ref]['sum'] += $balance;
                                break;

                            case '1.2.':
                                $addToSection('nomCurrentAssets');
                                $aBp['assets'][$ref]['sum'] += $balance;
                                break;

                            case '2.1.':
                                $addToSection('currentLiabilities');
                                $aBp['liabilities'][$ref]['sum'] += $balance;
                                break;

                            case '2.2.':
                                $addToSection('nomCurrentLiabilities');
                                $aBp['liabilities'][$ref]['sum'] += $balance;
                                break;

                            case '2.4.':
                                $addToSection('freeHeritage');
                                $aBp['liabilities'][$ref]['sum'] += $balance;
                                break;
                        }
                    }
                }
            }

            return [
                'name'      => $ragName,
                'companyName' => $company->name,
                'periodRange' => $periodRange,
                'aClosing'  => $aClosing,
                'response'  => $response,
                'fileOrder' => $this->fileOrder,
                'BP'        => $aBp,
                'DRE'       => $aDre,
            ];

        } catch (\Exception $e){
            dd($e->getMessage());
        }

    }
}
