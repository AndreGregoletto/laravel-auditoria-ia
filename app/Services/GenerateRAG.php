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
            ->where('file_status_id', 2)
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
            $ragName   = (string) "RAG - {$company->name} | " . __('files.period') . ":";

            foreach ($this->fileOrder as $id => $f){
                $ragName .= " {$f['reference_month']}/{$f['reference_year']}";
                $response[$id] = $this->getDataFile($id);
            }


            dd($ragName);


        } catch (\Exception $e){
            dd($e);
        }

        return [];
    }
}
