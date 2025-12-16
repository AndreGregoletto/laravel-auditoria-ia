<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public function render()
    {
        return view('livewire.language-switcher');
    }

    public function changeLocale($locale)
    {
        session()->put('locale', $locale);
        session()->save();

        return $this->redirect(request()->header('Referer'));
    }
}
