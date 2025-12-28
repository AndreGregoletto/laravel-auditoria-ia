<?php

namespace App\Jobs;

use App\Models\ImportFile;
use App\Models\Queue\TrialBalanceData;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ProcessTrialBalanceImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $importFileId;

    public function __construct(int $importFileId)
    {
        $this->importFileId = $importFileId;
    }

    public function handle(AIService $aiService): void
    {
        DB::beginTransaction();

        try {
            $importFile = ImportFile::where('id', $this->importFileId)
                ->where('file_status_id', 2)
                ->where('file_step_id', 5)
                ->firstOrFail();

            $importFile->update(['file_step_id' => 1]);

            $fileName = "{$importFile->user_id}-{$importFile->company_id}-{$importFile->reference_year}-{$importFile->reference_month}-{$importFile->file_name}";

            $relativePath = "balance/{$fileName}";

            if (!Storage::disk('private')->exists($relativePath)) {
                throw new \Exception(__('error.file_not_found_on_disk').": {$relativePath}");
            }

            $collection = Excel::toCollection(null, $relativePath, 'private')->first();

            if ($collection->isEmpty()) {
                throw new \Exception(__('error.the_file_is_empty'));
            }

            $headers    = $collection->first()->toArray();
            $sampleRows = $collection->slice(1, 5)->values()->toArray();

            $columnMap = $aiService->mapTrialBalanceColumns(
                headers: $headers,
                sample: $sampleRows
            );

            if (!isset($columnMap['account'])) {
                throw new \Exception(__('error.the_ai_was_unable_to_identify_the_necessary_columns'));
            }

            $batchData = [];
            $now       = Carbon::now();
            $batchSize = 1000;

            foreach ($collection->slice(1) as $line => $row) {
                $row = $row->toArray();

                if (empty($row[$columnMap['account']])) continue;

                $batchData[] = [
                    'file_id'          => $importFile->id,
                    'company_id'       => $importFile->company_id,
                    'file_line'        => $line + 1,
                    'account'          => $row[$columnMap['account']] ?? '',
                    'description'      => $row[$columnMap['description']] ?? '',
                    'previous_balance' => $this->toDecimal($row[$columnMap['previous_balance']] ?? ''),
                    'debit'            => $this->toDecimal($row[$columnMap['debit']] ?? ''),
                    'credit'           => $this->toDecimal($row[$columnMap['credit']] ?? ''),
                    'monthly_activity' => $this->toDecimal($row[$columnMap['monthly_activity']] ?? ''),
                    'closing_balance'  => $this->toDecimal($row[$columnMap['closing_balance']] ?? ''),
                    'red_flag'         => 0,
                    'status'           => 1,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];

                if (count($batchData) >= $batchSize) {
                    TrialBalanceData::insert($batchData);
                    $batchData = [];
                }
            }

            if (!empty($batchData)) {
                TrialBalanceData::insert($batchData);
            }

            $importFile->update(['file_step_id' => 2]);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            ImportFile::where('id', $this->importFileId)->update([
                'file_step_id'   => 3,
                'file_status_id' => 1,
                'error_log'      => substr($e->getMessage(), 0, 250)
            ]);

            Log::error("Erro na importação ID {$this->importFileId}: " . $e->getMessage());

            throw $e;
        }
    }

    private function toDecimal($value): ?float
    {
        if (is_null($value) || trim($value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = (string) $value;

        if (strrpos($value, ',') > strrpos($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {
            $value = str_replace(',', '', $value);
        }

        return (float) preg_replace('/[^\d.-]/', '', $value);
    }

    private function calcAbsolute(array $row, array $map): ?float
    {
        $current  = $this->toDecimal($row[$map['current_balance']]  ?? null);
        $previous = $this->toDecimal($row[$map['previous_balance']] ?? null);

        if ($current === null || $previous === null) {
            return null;
        }

        return $current - $previous;
    }
}
