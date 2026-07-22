<?php

use App\Http\Controllers\PessoaController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Pessoas\Index as PessoaIndex;

        /*
        |----------------------------------------------------------------------
        | Pessoas (módulo 02)
        |----------------------------------------------------------------------
        */
        Route::get('/pessoas', PessoaIndex::class)
            ->middleware('can:pessoas.visualizar')
            ->name('pessoas.index');
            
        Route::get('/pessoas/novo', [PessoaController::class, 'create'])->name('pessoas.create');
        Route::get('/pessoas/{pessoa}/edit', [PessoaController::class, 'edit'])->name('pessoas.edit');
        Route::post('/pessoas', [PessoaController::class, 'store'])->name('pessoas.store');
        Route::put('/pessoas/{pessoa}', [PessoaController::class, 'update'])->name('pessoas.update');
        Route::patch('/pessoas/{pessoa}/toggle-status', [PessoaController::class, 'alternarStatus'])->name('pessoas.toggle-status');
        Route::delete('/pessoas/{pessoa}', [PessoaController::class, 'destroy'])->name('pessoas.destroy');

        Route::patch('/pessoas/{pessoa}/usuario', [PessoaController::class, 'atualizarUsuario'])->name('pessoas.atualizar-usuario');
        Route::patch('/pessoas/{pessoa}/usuario/revogar', [PessoaController::class, 'revogarUsuario'])->name('pessoas.revogar');
        Route::patch('/pessoas/{pessoa}/usuario/reativar', [PessoaController::class, 'reativarUsuario'])->name('pessoas.reativar');