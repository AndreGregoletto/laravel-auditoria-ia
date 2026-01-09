<?php

use Illuminate\Support\Facades\Route;


Route::view('/', 'welcome');


Route::group(['prefix' => 'queue-import'], function(){
    Route::get('/my-files', \App\Livewire\Reports\Imports\MyFiles::class)
        ->middleware('auth')
        ->name('imports.my-files');
});


Route::get('/audits', function () {
    return \App\Models\git ::get();
});

require __DIR__.'/tools.php';
require __DIR__.'/profile.php';
require __DIR__.'/reports.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
