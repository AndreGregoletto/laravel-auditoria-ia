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

    public function startProcess(array $aFiles): array
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
            $endFile   = end($this->fileOrder);
            $ragName  .= "{$endFile['reference_month']}/{$endFile['reference_year']}";

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

            foreach ($trialBalance as $key => $tb){
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
                        'clear_account' => str_replace('.', '', $account),
                        'description'   => (string) $tb->description,
                        'balance'       => [],
                    ];
                }
                $response[$account]['balance'][$ref] = ($response[$account]['balance'][$ref] ?? 0) + (float) $tb->closing_balance;
            }

            return [
                'name'      => $ragName,
                'aClosing'  => $aClosing,
                'response'  => $response,
                'fileOrder' => $this->fileOrder,
            ];

        } catch (\Exception $e){
            dd($e);
        }

    }
}
