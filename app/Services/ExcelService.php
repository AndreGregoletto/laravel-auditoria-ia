<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ExcelService
{
    public function saveToQueue(File $file):bool
    {
        try {
            $response = true;

            dd($file);
        }catch (\Exception $e){
            Log::error('method => saveToQueue / '. $e->getMessage());
            $response = false;
        }

        return $response;
    }
}
