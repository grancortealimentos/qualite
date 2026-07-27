<?php

use App\Http\Controllers\PropriedadeController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Propriedades\Index as PropriedadeIndex;

// ------------------------------------------------------------------
// Módulo 05 - Propriedades
// ------------------------------------------------------------------
Route::prefix('propriedades')->name('propriedades.')->group( function () {
    Route::get('/', PropriedadeIndex::class)
        ->middleware('can:propriedades.visualizar')
        ->name('index');

    Route::get('/novo', [PropriedadeController::class, 'create'])
        ->middleware('can:propriedades.criar')
        ->name('create');
        
    Route::get('/{propriedade}/editar', [PropriedadeController::class, 'edit'])
        ->middleware('can:propriedades.editar')
        ->name('edit');

    Route::post('/', [PropriedadeController::class, 'store'])
            ->middleware('can:propriedades.criar')
            ->name('store');
        
    Route::put('/{propriedade}', [PropriedadeController::class, 'update'])
        ->middleware('can:propriedades.editar')
        ->name('update');

    Route::patch('/{propriedade}/status', [PropriedadeController::class, 'alterarStatus'])
        ->middleware('can:propriedades.alterarStatus')
        ->name('alterar-status');

    Route::delete('/{propriedade}', [PropriedadeController::class, 'destroy'])
        ->middleware('can:propriedades.excluir')
        ->name('destroy');
});