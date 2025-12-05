<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class TestExcel extends Form
{
    #[Validate('required|file|mimes:xlsx,xls,csv|max:5240')] // Máximo 10MB
    public $file;

    public function messages(): array
    {
        return [
            'file.required' => 'Por favor, selecione um arquivo para upload.',
            'file.mimes'    => 'O arquivo deve ser no formato Excel (xlsx, xls) ou CSV.',
            'file.max'      => 'O tamanho máximo do arquivo é de 10MB.',
        ];
    }
}
