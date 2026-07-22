<?php

use App\Http\Controllers\FilialController;
use App\Livewire\Filiais\Index as FilialIndex;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------------------------------------
| Filiais (módulo 06)
|----------------------------------------------------------------------
*/
Route::get('/filiais', FilialIndex::class)
    ->name('filiais.index');
    // ->middleware('can:filiais.viewAny');

Route::get('/filiais/criar', [FilialController::class, 'create'])
    ->name('filiais.create');
    // ->middleware('can:filiais.criar');

Route::get('/filiais/{filial}/editar', [FilialController::class, 'edit'])
    ->name('filiais.edit');
    // ->middleware('can:filiais.editar');

Route::post('/filiais', [FilialController::class, 'store'])
    ->name('filiais.store');
    // ->middleware('can:filiais.criar');

Route::put('/filiais/{filial}', [FilialController::class, 'update'])
    ->name('filiais.update');
    // ->middleware('can:filiais.editar');