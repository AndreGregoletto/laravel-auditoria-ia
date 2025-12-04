<?php

namespace App\Livewire;

use App\Livewire\Forms\TestExcel;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class SendExcel extends Component
{
    use WithFileUploads;

    public TestExcel $form;

    public function save()
    {
        $this->form->validate();

        $excelData = Excel::toCollection(collect(), $this->form->file);
        dd($excelData);

        return $this->redirect('posts');
    }

    public function render()
    {
        return view('excel');
    }
}
