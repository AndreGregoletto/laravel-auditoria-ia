<?php

use \App\Livewire\SendExcel                                  AS SendExcel;
use \App\Livewire\Tools\Processes                            AS Processes;
use \App\Livewire\Tools\Balance\ValidateTrialBalance         AS ValidateTrialBalance;
use App\Livewire\Tools\Balance\ValidateTrialBalanceEdit      AS ValidateTrialBalanceEdit;
use App\Livewire\Tools\Balance\ValidateTrialBalanceAI        AS ValidateTrialBalanceAI;
use App\Livewire\Tools\Balance\ValidateTrialBalanceAiPreview AS ValidateTrialBalanceAiPreview;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('ask-system', 'ask-system')->name('ask-system');

    Route::group(['prefix' => 'queue-import'], function(){
        Route::get('balancete', SendExcel::class)->name('balancete');
    });

    Route::get('processes', Processes::class)->name('processes');

    Route::group(['prefix' => 'processes'], function(){

        Route::group(['prefix' => 'validate'], function(){
            Route::get('/balance', ValidateTrialBalance::class)->name('validate-balance');
            Route::get('/{file}/edit', ValidateTrialBalanceEdit::class)->name('validate-edit');
            Route::get('/{file}/ai', ValidateTrialBalanceAi::class)->name('validate-ai');
            Route::get('/{file}/ai-preview', ValidateTrialBalanceAiPreview::class)->name('validate.ai-preview');
        });

    });

});

