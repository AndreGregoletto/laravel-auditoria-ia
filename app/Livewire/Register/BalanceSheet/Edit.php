<?php

namespace App\Livewire\Register\BalanceSheet;

use App\Models\BalanceSheet;
use Livewire\Component;

class Edit extends Component
{
    public BalanceSheet $balanceSheet;
    public array $form = [];

    public function mount(BalanceSheet $balanceSheet): void
    {
        $this->balanceSheet = $balanceSheet;
        $this->form = $balanceSheet->only([
            'code', 'name', 'company_tree_id',
            'company_id', 'status', 'parent_code',
            'sort_order', 'side', 'section', 'config_name'
        ]);
    }

    protected function rules(): array
    {
        return [
            'form.code'   => ['required', 'string', 'max:10'],
            'form.name'   => ['required', 'string', 'max:50'],
            'form.status' => ['boolean'],
        ];
    }

    public function save()
    {
        $this->validate();
        $this->balanceSheet->update($this->form);

        return redirect()->route('settings.register.file-step.index');
    }

    public function render()
    {
        return view('livewire.register.balance-sheet.edit')->layout('layouts.app');
    }
}
