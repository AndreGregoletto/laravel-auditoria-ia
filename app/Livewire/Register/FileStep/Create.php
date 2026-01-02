<?php

namespace App\Livewire\Register\FileStep;

use App\Models\FileStep;
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
            'form.name'      => ['required', 'string', 'max:255', 'unique:type_files,name'],
            'form.name_conf' => ['required', 'string', 'max:255', 'unique:type_files,name'],
            'form.status'    => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'form.name.required'       => __('error.name_required'),
            'form.name.string'         => __('error.name_string'),
            'form.name.max'            => __('error.name_max_255'),
            'form.name.unique'         => __('error.name_unique'),
            'form.name_conf.required'  => __('error.name_required'),
            'form.name_conf.string'    => __('error.name_string'),
            'form.name_conf.max'       => __('error.name_max_255'),
            'form.name_conf.unique'    => __('error.name_unique'),
            'form.status.boolean' => __('error.bool'),
        ];
    }

    public function save()
    {
        $this->validate(
            $this->rules(),
            $this->messages()
        );

        FileStep::create($this->form);

        return redirect()->route('settings.register.file-step.index');
    }

    public function render()
    {
        return view('livewire.register.file-step.create')->layout('layouts.app');
    }
}
