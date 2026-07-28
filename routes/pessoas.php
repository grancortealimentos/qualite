<?php

use App\Http\Controllers\PessoaController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Pessoas\Index as PessoaIndex;
use App\Livewire\Usuarios\GerenciarPermissoesUsuario;

/*
|----------------------------------------------------------------------
| Pessoas (módulo 02)
|----------------------------------------------------------------------
*/
Route::get('/pessoas', PessoaIndex::class)
    ->middleware('can:pessoas.visualizar')
    ->name('pessoas.index');
    
Route::get('/pessoas/novo', [PessoaController::class, 'create'])
    ->middleware('can:pessoas.criar')
    ->name('pessoas.create');

Route::get('/pessoas/{pessoa}/edit', [PessoaController::class, 'edit'])
    ->middleware(['can:pessoas.update', 'can:update,pessoa'])
    ->name('pessoas.edit');

Route::post('/pessoas', [PessoaController::class, 'store'])
    ->middleware('can:pessoas.criar')
    ->name('pessoas.store');

Route::put('/pessoas/{pessoa}', [PessoaController::class, 'update'])
    ->middleware(['can:pessoas.update', 'can:update,pessoa'])
    ->name('pessoas.update');
    
Route::patch('/pessoas/{pessoa}/toggle-status', [PessoaController::class, 'alternarStatus'])
    ->middleware(['can:pessoas.status', 'can:alterarStatus,pessoa'])
    ->name('pessoas.toggle-status');

Route::delete('/pessoas/{pessoa}', [PessoaController::class, 'destroy'])
    ->middleware(['can:pessoas.excluir', 'can:delete,pessoa'])
    ->name('pessoas.destroy');

Route::patch('/pessoas/{pessoa}/usuario', [PessoaController::class, 'atualizarUsuario'])
    ->middleware('can:pessoas.editar')
    ->name('pessoas.atualizar-usuario');

Route::patch('/pessoas/{pessoa}/usuario/revogar', [PessoaController::class, 'revogarUsuario'])
    ->middleware('can:pessoas.editar')
    ->name('pessoas.revogar');

Route::patch('/pessoas/{pessoa}/usuario/reativar', [PessoaController::class, 'reativarUsuario'])
    ->middleware('can:pessoas.editar')
    ->name('pessoas.reativar');

Route::get('/usuarios/{usuario}/permissoes', GerenciarPermissoesUsuario::class)
    ->middleware('can:usuarios.gerenciar_permissoes')
    ->name('usuarios.permissoes');