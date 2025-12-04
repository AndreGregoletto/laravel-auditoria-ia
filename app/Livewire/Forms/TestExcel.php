<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class TestExcel extends Form
{
    #[Validate('required|file|mimes:xlsx,xls,csv|max:10240')] // Máximo 10MB
    public $file;

}
