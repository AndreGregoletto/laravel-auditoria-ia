<?php

use App\Livewire\Reports\Companies          as ReportCompanies;
use App\Livewire\Reports\TreeCompany        as ReportTreeCompany;
use App\Livewire\Reports\TreeCompany\Index  as ReportTreeCompanyIndex;
use App\Livewire\Reports\UploadedFiles      as ReportUploadedFiles;
use \App\Livewire\Reports\Imports\MyFiles   as ReportMyFiles;

#Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/index', ReportCompanies::class)->name('index');
        Route::get('/tree', ReportTreeCompany::class)->name('tree');
        Route::get('/{company_tree}/tree_company', ReportTreeCompanyIndex::class)->name('index_tree_company');
    });

    Route::get('uploaded_files', ReportUploadedFiles::class)->name('uploaded_files');

    Route::group(['prefix' => 'queue-import'], function(){
        Route::get('/my-files', ReportMyFiles::class)
            ->name('imports.my-files');
    });

})->middleware(['auth', 'verified']);

