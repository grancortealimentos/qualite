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

    Route::post('/', [PropriedadeController::class, 'store'])
        ->middleware('can:propriedades.criar')
        ->name('store');

    Route::get('/{propriedade}/editar', [PropriedadeController::class, 'update'])
        ->middleware('can:propriedades.editar')
        ->name('edit');

    Route::put('/{propriedade}', [PropriedadeController::class, 'update'])
        ->middleware('can:propriedades.editar')
        ->name('update');

    Route::patch('/{propriedades}/status', [PropriedadeController::class, 'alterarStatus'])
        ->middleware('can:propriedades.alterarStatus')
        ->name('alterar-status');

    Route::delete('/{propriedade}', [PropriedadeController::class, 'destroy'])
        ->middleware('can:propriedades.excluir')
        ->name('destroy');
});