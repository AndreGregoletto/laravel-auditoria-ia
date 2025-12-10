<?php

namespace App\Livewire;

use App\Livewire\Forms\QueueImport\Balancete;
use App\Models\ImportFile;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class SendExcel extends Component
{
    use WithFileUploads;

    public Balancete $form;

    public function save()
    {
        try {

            $this->form->validate();
            $file = $this->form->file;

            $idUser       = Auth::user()->id;
            $originalName = $file->getClientOriginalName();
            $extension    = $file->getClientOriginalExtension();
            $size         = $file->getSize();
            $path         = $idUser . '-' . $originalName;

            if(Storage::disk('private')->exists($path)) {
                throw ValidationException::withMessages([
                    'form.file' => 'Este arquivo já foi enviado por você.',
                ]);
            }

            $storePath = $file->storeAS('', $path, 'private');

            if(!$storePath) {
                throw new \Exception('Falha ao salvar o arquivo.');
            }

            $importFile = [
                'user_id'        => $idUser,
                'file_name'      => $originalName,
                'file_extension' => $extension,
                'file_service'   => 1,
                'file_size'      => "{$size}",
            ];

            if(Storage::disk('private')->path($storePath)){
                if(!ImportFile::create($importFile)){
                    Storage::disk('private')->delete($storePath);
                    throw new \Exception('Falha ao salvar dados do arquivos');
                }
            }

            $this->reset('form.file');
            session()->flash('success', 'Arquivo enviado com sucesso!');

        }catch (ValidationException $e){
            throw $e;
        }catch (\Exception $e){
            $this->addError('form.file', $e->getMessage());
        }
    }

    public function render()
    {
        return view('excel');
    }
}
