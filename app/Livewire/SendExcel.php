<?php

namespace App\Livewire;

use App\Jobs\ProcessTrialBalanceImport;
use App\Livewire\Forms\QueueImport\Balancete;
use App\Models\Company;
use App\Models\ImportFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
    public array $months = [];
    public array $years  = [];

    private string $pathBalance;

    public function mount() :void
    {
        $currentYear = now()->year;

        $this->companies = Company::select(['id', 'name', 'commercial_name'])->where('status', 1)->orderByRaw("COALESCE(commercial_name, name)")->get();
        $this->months = [
            1 => __('labels.january'),
            2 => __('labels.february'),
            3 => __('labels.march'),
            4 => __('labels.april'),
            5 => __('labels.may'),
            6 => __('labels.june'),
            7 => __('labels.july'),
            8 => __('labels.august'),
            9 => __('labels.september'),
            10 => __('labels.october'),
            11 => __('labels.november'),
            12 => __('labels.december'),
        ];

        $this->years = [$currentYear, $currentYear - 1 ];
    }

    public function getPath(): void
    {
        $path = env('IMPORT_BALANCE', 'balance/');
        $this->pathBalance = str_ends_with($path, '/') ? $path : $path . '/';
    }

    public function save()
    {
        try {
            DB::beginTransaction();

            $this->form->validate();
            $file = $this->form->file;
            $now  = now()->year;

            if(!Company::where('id', $this->form->company_id)->where('status', 1)->exists()){
                throw ValidationException::withMessages([
                    'form' => __('error.company_not_found'),
                ]);
            }

            $year = (int) $this->form->reference_year;

            if(!in_array($year, [$now, $now - 1])){
                throw ValidationException::withMessages([
                    'form' => __('error.year_out_of_limit'),
                ]);
            }

            $idUser       = Auth::id();
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $size         = $file->getSize();

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
                'file_status_id'  => 2,
            ];

            if(!$importFileRecord = ImportFile::create($importFileData)){
                DB::rollBack();
                throw ValidationException::withMessages([
                    'form' => __('error.error_importing_id'),
                ]);
            }

            $this->getPath();
            $storePath = $file->storeAs($this->pathBalance, "{$importFileRecord->id}.{$extension}", 'private');

            if (!$storePath) {
                DB::rollBack();
                throw new \Exception(__('error.failed_to_save_the_file'));
            }

            ProcessTrialBalanceImport::dispatch($importFileRecord->id);

            $this->reset('form');

            session()->flash('success', __('success.file_sent'));

            DB::commit();
        } catch (ValidationException $e) {
            DB::rollBack();
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
