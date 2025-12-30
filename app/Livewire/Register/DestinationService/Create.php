<?php

namespace App\Livewire\Register\DestinationService;

use App\Models\TypeFile;
use Livewire\Component;

class Create extends Component
{
    public array $form = [
        'name'   => '',
        'status' => true,
    ];

    protected function rules(): array
    {
        return [
            'form.name'   => ['required', 'string', 'max:255', 'unique:type_files,name'],
            'form.status' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'form.name.required'  => __('error.name_required'),
            'form.name.string'    => __('error.name_string'),
            'form.name.max'       => __('error.name_max_255'),
            'form.name.unique'    => __('error.name_unique'),
            'form.status.boolean' => __('error.bool'),
        ];
    }

    public function save()
    {
        $this->validate(
            $this->rules(),
            $this->messages()
        );

        TypeFile::create($this->form);

        return redirect()->route('settings.register.destination-service.index');
    }

    public function render()
    {
        return view('livewire.register.destination-service.create')->layout('layouts.app');
    }
}
