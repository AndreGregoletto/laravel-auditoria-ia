<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Company\Index  as CompanyIndex;
use App\Livewire\Company\Create as CompanyCreate;
use App\Livewire\Company\Edit   as CompanyEdit;

use App\Livewire\CompanyTree\Index  as CompanyTreeIndex;
use App\Livewire\CompanyTree\Create as CompanyTreeCreate;
use App\Livewire\CompanyTree\Edit   as CompanyTreeEdit;

use App\Livewire\CompanyTree\OrganizationalChart\Index  as orgChart;

use App\Livewire\Reports\Companies          as ReportCompanies;
use App\Livewire\Reports\TreeCompany        as ReportTreeCompany;
use App\Livewire\Reports\TreeCompany\Index  as ReportTreeCompanyIndex;
use App\Livewire\Reports\UploadedFiles      as ReportUploadedFiles;

use App\Livewire\Register\DestinationService\Index  as DestinationServiceIndex;
use App\Livewire\Register\DestinationService\Create as DestinationServiceCreate;
use App\Livewire\Register\DestinationService\Edit   as DestinationServiceEdit;

use App\Livewire\Register\FileStatus\Index  as FileStatusIndex;
use App\Livewire\Register\FileStatus\Create as FileStatusCreate;
use App\Livewire\Register\FileStatus\Edit   as FileStatusEdit;

use App\Livewire\Register\FileStep\Index  as FileStepIndex;
use App\Livewire\Register\FileStep\Create as FileStepCreate;
use App\Livewire\Register\FileStep\Edit   as FileStepEdit;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('ask-system', 'ask-system')
    ->middleware(['auth', 'verified'])
    ->name('ask-system');

Route::group(['prefix' => 'queue-import'], function(){

    Route::get('balancete', \App\Livewire\SendExcel::class)
        ->middleware(['auth', 'verified'])
        ->name('balancete');

});
//Se for possível abrir por card de cada empresa, razão social (nome dado a entidade na sua abertura conforme cartório),
//nome fantasia (apelido comumente falado pelas pessoas), CNPJ (número da mesma) e se fosse o caso, empresa que controla
//a mesma e e-mail dos responsáveis, mas essa parte de responsável deveria ser algo em que nós da auditoria/contabilidade
//iriamos preencher, por conta da rotatividade, como nosso mercado é muito rotativo e cíclico o cara que é responsável
//hoje não necessariamente vai ser o responsável daqui a 3 meses.

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

#Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/index', ReportCompanies::class)->name('index');
        Route::get('/tree', ReportTreeCompany::class)->name('tree');
        Route::get('/{company_tree}/tree_company', ReportTreeCompanyIndex::class)->name('index_tree_company');
    });

    Route::get('uploaded_files', ReportUploadedFiles::class)->name('uploaded_files');
})->middleware(['auth', 'verified']);

#Settings
Route::prefix('settings')->name('settings.')->group(function (){
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', CompanyIndex::class)->name('index');
        Route::get('/create', CompanyCreate::class)->name('create');
        Route::get('/{company}/edit', CompanyEdit::class)->name('edit');
    })->middleware(['auth', 'verified']);

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
    });
})->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';
