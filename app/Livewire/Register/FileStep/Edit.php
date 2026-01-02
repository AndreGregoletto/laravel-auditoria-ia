<?php

namespace App\Livewire\Register\FileStep;

use App\Models\FileStep;
use Livewire\Component;

class Edit extends Component
{
    public FileStep $fileStep;
    public array $form = [];

    public function mount(FileStep $fileStep): void
    {
        $this->fileStep = $fileStep;
        $this->form = $fileStep->only(['name', 'name_conf', 'status']);
    }

    protected function rules(): array
    {
        return [
            'form.name'      => ['required', 'string', 'max:255'],
            'form.name_conf' => ['required', 'string', 'max:255'],
            'form.status'    => ['boolean'],
        ];
    }

    public function save()
    {
        $this->validate();
        $this->fileStep->update($this->form);

        return redirect()->route('settings.register.file-step.index');
    }
    public function render()
    {
        return view('livewire.register.file-step.edit')->layout('layouts.app');
    }
}
