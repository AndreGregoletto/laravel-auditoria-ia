<?php

namespace App\Livewire;

use App\Jobs\ProcessTrialBalanceImport;
use App\Livewire\Forms\QueueImport\Balancete;
use App\Models\Company;
use App\Models\ImportFile;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class SendExcel extends Component
{
    use WithFileUploads;

    public Balancete $form;
    public Collection $companies;

    private string $pathBalance;

    public function mount() :void
    {
        $this->companies = Company::orderBy('name')->get(['id', 'name']);
    }

    public function getPath(): void
    {
        $path = env('IMPORT_BALANCE', 'balance/');
        $this->pathBalance = str_ends_with($path, '/') ? $path : $path . '/';
    }

    public function save()
    {
        try {
            $this->form->validate();
            $file = $this->form->file;

            $idUser       = Auth::id();
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $size         = $file->getSize();
            $fileName     = "{$idUser}-{$this->form->company_id}-{$this->form->reference_year}-{$this->form->reference_month}-{$originalName}";

            $failedImport = ImportFile::where('user_id', $idUser)
                ->where('file_name', $originalName)
                ->where('file_service', 1)
                ->where('file_status_id', 1)
                ->whereIn('file_step_id', [3, 4])
                ->first();

            $this->getPath();

            $finalPath = $this->pathBalance . $fileName;

            if ($failedImport) {
                if (Storage::disk('private')->exists($finalPath)) {
                    $errorPath = "error/{$this->pathBalance}{$fileName}";
//                        Storage::disk('private')->makeDirectory("error/{$this->pathBalance}");
                    Storage::disk('private')->move($finalPath, $errorPath);
                }

                $failedImport->update(['file_status_id' => 0]);

            } else {
                if (Storage::disk('private')->exists($finalPath)) {
                    throw ValidationException::withMessages([
                        'form' => __('error.you_have_already_send_this_file'),
                    ]);
                }
            }

            $storePath = $file->storeAs($this->pathBalance, $fileName, 'private');

            if (!$storePath) {
                throw new \Exception(__('error.failed_to_save_the_file'));
            }

            $importFileData = [
                'user_id'         => $idUser,
                'company_id'      => $this->form->company_id,
                'reference_month' => $this->form->reference_month,
                'reference_year'  => $this->form->reference_year,
                'file_name'       => $originalName,
                'file_extension'  => $extension,
                'file_service'    => 1,
                'file_step_id'    => 5,
                'file_size'       => $size,
                'file_status_id'  => 1,
//                'error_log'
            ];

            $importFileRecord = ImportFile::create($importFileData);

            if (!$importFileRecord) {
                Storage::disk('private')->delete($storePath);
                throw new \Exception(__('error.failed_to_save_the_file'));
            }

            ProcessTrialBalanceImport::dispatch($importFileRecord->id);

            $this->reset('form');

            session()->flash('success', __('success.file_sent'));

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->addError('form', $e->getMessage());
        }
    }

    public function render()
    {
        return view('excel', [
            'companies' => $this->companies,
        ]);
    }
}
