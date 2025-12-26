<?php

namespace App\Livewire\Forms\QueueImport;

use Livewire\Attributes\Validate;
use Livewire\Form;

class Balancete extends Form
{
    #[Validate('required|file|mimes:xlsx,xls,csv|max:5240')] // Máximo 10MB
    public $file;

    public ?int $company_id = null;
    public ?int $reference_month = null;
    public ?int $reference_year = null;
    public function rules(): array
    {
        return [
            'file'            => ['required', 'file', 'mimes:xlsx,xls', 'max:20480'],
            'company_id'      => ['required', 'integer', 'min:1'],
            'reference_month' => ['required', 'integer', 'between:1,12'],
            'reference_year'  => ['required', 'integer', 'between:2000,2100'],
        ];
    }

    public function messages(): array
    {

        return [
            'file.required'           => __('error.please_select_a_file_to_upload'),
            'file.mimes'               => __('error.the_file_must_be_in_excel_format'),
            'file.max'                 => __('error.the_maximum_file_size_is_10_mb'),
            'company_id.required'      => __('error.name_required'),
            'reference_month.required' => __('error.name_required'),
            'reference_year.required'  => __('error.name_required'),
            'reference_year.mimes'     => __('error.the_file_must_be_in_excel_format'),
            'reference_year.max'       => __('error.the_maximum_file_size_is_10_mb'),
        ];
    }
}
