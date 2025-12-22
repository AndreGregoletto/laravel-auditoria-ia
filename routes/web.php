<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Company\Index  as CompanyIndex;
use App\Livewire\Company\Create as CompanyCreate;
use App\Livewire\Company\Edit   as CompanyEdit;

use App\Livewire\CompanyTree\Index  as CompanyTreeIndex;
use App\Livewire\CompanyTree\Create as CompanyTreeCreate;

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

#Settings
Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('/', CompanyIndex::class)->name('index');
    Route::get('/create', CompanyCreate::class)->name('create');
    Route::get('/{company}/edit', CompanyEdit::class)->name('edit');
})->middleware(['auth', 'verified']);

Route::prefix('companies_tree')->name('companies_tree.')->group(function () {
    Route::get('/', CompanyTreeIndex::class)->name('index');
    Route::get('/create', CompanyTreeCreate::class)->name('create');
//    Route::get('/{company}/edit', CompanyEdit::class)->name('edit');
})->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';
