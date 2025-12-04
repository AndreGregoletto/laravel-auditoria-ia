<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


//Route::view('excel', 'excel')
//    ->middleware(['auth', 'verified'])
//    ->name('excel');

Route::get('excel', \App\Livewire\SendExcel::class)
    ->middleware(['auth', 'verified'])
    ->name('excel');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/audits', function () {
    return \App\Models\git ::get();
//    return AuditLog::latest()->paginate(50);
});

require __DIR__.'/auth.php';
