<?php

namespace App\Services;

use App\Models\ImportFile;
use App\Models\Queue\TrialBalanceData;
use Illuminate\Support\Collection;

class GenerateRAG
{
    public Collection $importFile;

    public function getFiles($aFiles): void
    {
        $this->importFile = ImportFile::whereIn('id', $aFiles)
            ->where('file_step_id', 2)
            ->where('file_status_id', 2)
            ->orderBy('reference_year', 'ASC')
            ->orderBy('reference_month', 'ASC')
            ->get();
    }

    public function getDataFile($aFile): Collection
    {
        return TrialBalanceData::query()
            ->whereIn('file_id', $aFile)
            ->where('status')
            ->get();
    }

    public function validatorFiles(array $aFiles): bool
    {
        $this->getFiles($aFiles);
        $response  = true;
        $files     = (array) [];
        $queue     = $this->importFile;
        $idCompany = (int) 0;

        foreach ($queue as $q){
            if($q->company_id !== $idCompany && $idCompany !== 0){
                $response = false;
                continue;
            }
            $files[$q->id] = $q->id;
            $idCompany = $q->company_id;
        }

        if($response){
            $response = count($files) >= 2 ?? false;
        }

        return $response;
    }

    public function startProcess(array $aFiles): array
    {
        try {
            if(!$this->validatorFiles($aFiles)){
                throw new \Exception('Arquivos invalidos');
            }

            dd($this->getDataFile($aFiles));


        } catch (\Exception $e){
            dd($e);
        }

        return [];
    }
}
