<?php

use \App\Livewire\SendExcel as SendExcel;
use \App\Livewire\Tools\Processes as Processes;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('ask-system', 'ask-system')->name('ask-system');

    Route::group(['prefix' => 'queue-import'], function(){
        Route::get('balancete', SendExcel::class)->name('balancete');
    });

    Route::get('processes', Processes::class)->name('processes');
});

