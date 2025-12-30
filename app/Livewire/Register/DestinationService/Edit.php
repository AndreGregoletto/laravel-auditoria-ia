<?php

namespace App\Livewire\Register\DestinationService;

use App\Models\TypeFile;
use Livewire\Component;

class Edit extends Component
{
    public TypeFile $typeFile;
    public array $form = [];

    public function mount(TypeFile $typeFile): void
    {
        $this->typeFile = $typeFile;
        $this->form = $typeFile->only(['name', 'status']);
    }

    protected function rules(): array
    {
        return [
            'form.name'   => ['required', 'string', 'max:255'],
            'form.status' => ['boolean'],
        ];
    }

    public function save()
    {
        $this->validate();
        $this->typeFile->update($this->form);

        return redirect()->route('settings.register.destination-service.index');
    }

    public function render()
    {
        return view('livewire.register.destination-service.edit')->layout('layouts.app');
    }
}
