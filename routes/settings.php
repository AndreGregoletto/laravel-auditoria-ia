<?php

use App\Livewire\Company\Index  as CompanyIndex;
use App\Livewire\Company\Create as CompanyCreate;
use App\Livewire\Company\Edit   as CompanyEdit;

use App\Livewire\CompanyTree\Index  as CompanyTreeIndex;
use App\Livewire\CompanyTree\Create as CompanyTreeCreate;
use App\Livewire\CompanyTree\Edit   as CompanyTreeEdit;

use App\Livewire\CompanyTree\OrganizationalChart\Index  as orgChart;

use App\Livewire\Register\DestinationService\Index  as DestinationServiceIndex;
use App\Livewire\Register\DestinationService\Create as DestinationServiceCreate;
use App\Livewire\Register\DestinationService\Edit   as DestinationServiceEdit;

use App\Livewire\Register\FileStatus\Index  as FileStatusIndex;
use App\Livewire\Register\FileStatus\Create as FileStatusCreate;
use App\Livewire\Register\FileStatus\Edit   as FileStatusEdit;

use App\Livewire\Register\FileStep\Index  as FileStepIndex;
use App\Livewire\Register\FileStep\Create as FileStepCreate;
use App\Livewire\Register\FileStep\Edit   as FileStepEdit;

use App\Livewire\Register\BalanceSheet\Index  as BalanceSheetIndex;
use App\Livewire\Register\BalanceSheet\Create as BalanceSheetCreate;
use App\Livewire\Register\BalanceSheet\Edit   as BalanceSheetEdit;

use App\Livewire\Register\IncomeStatement\Index  as IncomeStatementIndex;
use App\Livewire\Register\IncomeStatement\Create as IncomeStatementCreate;
use App\Livewire\Register\IncomeStatement\Edit   as IncomeStatementEdit;


Route::prefix('settings')->name('settings.')->group(function (){
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', CompanyIndex::class)->name('index');
        Route::get('/create', CompanyCreate::class)->name('create');
        Route::get('/{company}/edit', CompanyEdit::class)->name('edit');
    });

    Route::prefix('companies_tree')->name('companies_tree.')->group(function () {
        Route::get('/', CompanyTreeIndex::class)->name('index');
        Route::get('/create', CompanyTreeCreate::class)->name('create');
        Route::get('/{company_tree}/edit', CompanyTreeEdit::class)->name('edit');

        Route::prefix('organizational_chart')->name('organizational_chart.')->group(function () {
            Route::get('{company_tree}/org_chart', orgChart::class)->name('index');
        });

    });

    Route::prefix('register')->name('register.')->group(function () {
        Route::prefix('destination-service')->name('destination-service.')->group(function () {
            Route::get('/', DestinationServiceIndex::class)->name('index');
            Route::get('/create', DestinationServiceCreate::class)->name('create');
            Route::get('/{typeFile}/edit', DestinationServiceEdit::class)->name('edit');
        });

        Route::prefix('file-status')->name('file-status.')->group(function () {
            Route::get('/', FileStatusIndex::class)->name('index');
            Route::get('/create', FileStatusCreate::class)->name('create');
            Route::get('/{fileStatus}/edit', FileStatusEdit::class)->name('edit');
        });

        Route::prefix('file-step')->name('file-step.')->group(function () {
            Route::get('/', FileStepIndex::class)->name('index');
            Route::get('/create', FileStepCreate::class)->name('create');
            Route::get('/{fileStep}/edit', FileStepEdit::class)->name('edit');
        });

        Route::prefix('balance-sheet-classification')->name('asset-base-classification.')->group(function () {
            Route::get('/', BalanceSheetIndex::class)->name('index');
            Route::get('/create', BalanceSheetCreate::class)->name('create');
            Route::get('/{balanceSheet}/edit', BalanceSheetEdit::class)->name('edit');
        });

        Route::prefix('income-statement-classification')->name('income-statement-classification.')->group(function () {
            Route::get('/', IncomeStatementIndex::class)->name('index');
            Route::get('/create', IncomeStatementCreate::class)->name('create');
            Route::get('/{incomeStatement}/edit', IncomeStatementEdit::class)->name('edit');
        });
    });
})->middleware(['auth', 'verified']);
