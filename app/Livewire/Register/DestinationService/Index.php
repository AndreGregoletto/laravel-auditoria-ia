<?php

namespace App\Livewire\Register\DestinationService;

use App\Http\Controllers\Register\DestinationServiceController;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getData(string $s)
    {
        $controller = new DestinationServiceController();

        return $controller->index($s);
    }

    public function render()
    {
        $term     = trim($this->search);
        $response = $this->getData($term);

        return view('livewire.register.destination-service.index',
            compact('response')
        )->layout('layouts.app');
    }
}
