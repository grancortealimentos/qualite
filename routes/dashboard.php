<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\PessoaController;
use App\Livewire\Pessoas\Index as PessoaIndex;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('logout', [LogoutController::class, 'store'])->name('logout');

    // Troca de senha: autenticada, mas SEM o middleware de bloqueio
    Route::get('alterar-senha', [ChangePasswordController::class, 'edit'])->name('password.change');
    Route::put('alterar-senha', [ChangePasswordController::class, 'update']);

    // Tudo que exige senha já trocada vai aqui dentro
    Route::middleware('password.changed')->group(function () {
        Route::view('dashboard', 'dashboard.index')->name('dashboard');
    });

    //Pessoas
    Route::get('/pessoas', PessoaIndex::class)->name('pessoas.index');
    Route::get('/pessoas/novo', [PessoaController::class, 'create'])->name('pessoas.create');
    Route::get('/pessoas/{pessoa}/edit', [PessoaController::class, 'edit'])->name('pessoas.edit');
    Route::post('/pessoas', [PessoaController::class, 'store'])->name('pessoas.store');
    Route::put('/pessoas/{pessoa}', [PessoaController::class, 'update'])->name('pessoas.update');
    Route::patch('/pessoas/{pessoa}/toggle-status', [PessoaController::class, 'alternarStatus'])->name('pessoas.toggle-status');
    Route::delete('/pessoas/{pessoa}', [PessoaController::class, 'destroy'])->name('pessoas.destroy');
});