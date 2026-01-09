<?php

use App\Livewire\Reports\Profile\Message\Index as ReportMessageIndex;

Route::prefix('profile')->name('profile.')->group(function () {
    Route::view('', 'profile')->name('profile');

    Route::get('/message', ReportMessageIndex::class)->name('message');
})->middleware('auth');



