<?php

namespace App\Livewire\Register\FileStatus;

use App\Models\FileStatus;
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

    public function render()
    {
        $term = trim($this->search);
        $response = FileStatus::query()
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', "%{$term}%")
                        ->orWhere('name_conf', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.register.file-status.index',
            compact('response')
        )->layout('layouts.app');
    }
}
