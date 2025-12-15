<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::group(['prefix' => 'queue-import'], function(){

    Route::get('balancete', \App\Livewire\SendExcel::class)
        ->middleware(['auth', 'verified'])
        ->name('balancete');

});

Route::group(['prefix' => 'queue-import'], function(){
    Route::get('/my-files', \App\Livewire\Reports\Imports\MyFiles::class)
        ->middleware('auth')
        ->name('imports.my-files');
});


Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/audits', function () {
    return \App\Models\git ::get();
//    return AuditLog::latest()->paginate(50);
});

require __DIR__.'/auth.php';
