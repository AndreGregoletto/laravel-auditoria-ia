<?php

namespace App\Livewire\Register\FileStatus;

use App\Models\FileStatus;
use Livewire\Component;

class Edit extends Component
{
    public FileStatus $fileStatus;
    public array $form = [];

    public function mount(FileStatus $fileStatus): void
    {
        $this->fileStatus = $fileStatus;
        $this->form = $fileStatus->only(['name', 'name_conf', 'status']);
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
        $this->fileStatus->update($this->form);

        return redirect()->route('settings.register.file-status.index');
    }

    public function render()
    {
        return view('livewire.register.file-status.edit')->layout('layouts.app');
    }
}
