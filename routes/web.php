<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/audits', function () {
    return \App\Models\git ::get();
});

require __DIR__.'/tools.php';
require __DIR__.'/profile.php';
require __DIR__.'/reports.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
