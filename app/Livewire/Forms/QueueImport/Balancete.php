<?php

namespace App\Livewire\Forms\QueueImport;

use Livewire\Attributes\Validate;
use Livewire\Form;

class Balancete extends Form
{
    #[Validate('required|file|mimes:xlsx,xls,csv|max:5240')] // Máximo 10MB
    public $file;

    public function messages(): array
    {

        return [
            'file.required' => __('error.please_select_a_file_to_upload'),
            'file.mimes'    => __('error.the_file_must_be_in_excel_format'),
            'file.max'      => __('error.the_maximum_file_size_is_10_mb'),
        ];
    }
}
