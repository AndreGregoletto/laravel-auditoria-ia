<?php

use Illuminate\Support\Facades\Route;

use App\Models\AuditLog;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/audits', function () {
    return AuditLog::get();
//    return AuditLog::latest()->paginate(50);
});
