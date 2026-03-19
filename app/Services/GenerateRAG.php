<?php

namespace App\Services;

use App\Models\Company;
use App\Models\ImportFile;
use App\Models\Queue\TrialBalanceData;
use Dotenv\Parser\Value;
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
            if (!$this->validatorFiles($aFiles)) {
                throw new \Exception(__('error.invalid_file'));
            }

            $idCompany = reset($this->fileOrder)['company_id'];
            $company   = Company::find($idCompany);

            $response = [];
            $aClosing = [];
            $idFile   = null;
            $file     = null;

            $startFile = reset($this->fileOrder);
            $endFile   = end($this->fileOrder);

            $startPeriodStr = sprintf('%02d/%d', $startFile['reference_month'], $startFile['reference_year']);
            $endPeriodStr   = sprintf('%02d/%d', $endFile['reference_month'], $endFile['reference_year']);
            $periodRange    = "{$startPeriodStr} a {$endPeriodStr}";

            $initialPeriod = "{$startFile['reference_month']}/{$startFile['reference_year']}";
            $finalPeriod   = "{$endFile['reference_month']}/{$endFile['reference_year']}";

            $ragName = "RAG - {$company->name} | " . __('files.period') . ": {$finalPeriod}";

            $getClassification = new TrialBalanceAiSuggester();
            $classification    = $getClassification->getClassificationDesc(end($aFiles));

            $trialBalance = TrialBalanceData::query()
                ->whereIn('file_id', array_keys($this->fileOrder))
                ->where('balance_included', 1)
                ->where('status', 1)
                ->orderBy('file_id')
                ->orderBy('file_line')
                ->get([
                    'account',
                    'description',
                    'closing_balance',
                    'file_id',
                    'balance_last_decision_id',
                    'balance_decision_source',
                    'balance_sheet_id',
                    'income_statement_id',
                ]);

            $filesById = ImportFile::query()
                ->whereIn('id', array_keys($this->fileOrder))
                ->get()
                ->keyBy('id');

            $closingByFile = TrialBalanceData::query()
                ->selectRaw('file_id, SUM(closing_balance) as total')
                ->whereIn('file_id', array_keys($this->fileOrder))
                ->where('balance_included', 1)
                ->groupBy('file_id')
                ->pluck('total', 'file_id')
                ->toArray();

            $bp  = [];
            $dre = [];

            foreach ($classification as $group => $items) {
                foreach ($items as $id => $accountPrefix) {
                    if ($group === 'bp') {
                        $bp[$id] = 0.0;
                    }

                    if ($group === 'dre') {
                        $dre[$id] = 0.0;
                    }
                }
            }

            $classify = [
                $initialPeriod => [
                    'bp'  => $bp,
                    'dre' => $dre,
                ],
                $finalPeriod => [
                    'bp'  => $bp,
                    'dre' => $dre,
                ],
            ];

            foreach ($trialBalance as $tb) {
                if ($idFile !== $tb->file_id) {
                    $idFile = $tb->file_id;
                    $file   = $filesById[$idFile] ?? null;

                    if (!$file) {
                        continue;
                    }
                }

                $ref = "{$file->reference_month}/{$file->reference_year}";
                $aClosing[$ref] = (float) ($closingByFile[$idFile] ?? 0.0);

                $account = trim((string) $tb->account);

                if (!isset($response[$account])) {
                    $response[$account] = [
                        'clear_account' => str_replace('.', '', $account),
                        'description'   => (string) $tb->description,
                        'balance'       => [],
                    ];
                }

                $response[$account]['balance'][$ref] =
                    (float) ($response[$account]['balance'][$ref] ?? 0.0) + (float) $tb->closing_balance;

                if (!$download || !in_array($ref, [$initialPeriod, $finalPeriod], true)) {
                    continue;
                }

                $type = match (true) {
                    !empty($tb->balance_sheet_id)    => 'bp',
                    !empty($tb->income_statement_id) => 'dre',
                    default                          => null,
                };

                if ($type === null || empty($classification[$type])) {
                    continue;
                }

                foreach ($classification[$type] as $id => $acc) {
                    $acc = trim((string) $acc);

                    if ($acc === '') {
                        continue;
                    }

                    if (str_starts_with((string) $tb->account, $acc)) {
                        if (!isset($classify[$ref][$type][$id])) {
                            $classify[$ref][$type][$id] = 0.0;
                        }

                        $classify[$ref][$type][$id] += (float) $tb->closing_balance;
                        break;
                    }
                }
            }

            return [
                'name'        => $ragName,
                'companyName' => $company->name,
                'periodRange' => $periodRange,
                'aClosing'    => $aClosing,
                'response'    => $response,
                'fileOrder'   => $this->fileOrder,
                'groupedSheets' => $classify
            ];
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
