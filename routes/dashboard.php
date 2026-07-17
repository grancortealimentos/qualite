<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\PapelController;
use App\Livewire\Papeis\Index as PapeisIndex;
use App\Livewire\Pessoas\Index as PessoaIndex;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('logout', [LogoutController::class, 'store'])->name('logout');

    // Troca de senha: autenticada, mas SEM o middleware de bloqueio.
    // Precisa ficar acessível justamente para quem está bloqueado por ele,
    // senão o usuário cai em loop de redirecionamento.
    Route::get('alterar-senha', [ChangePasswordController::class, 'edit'])->name('password.change');
    Route::put('alterar-senha', [ChangePasswordController::class, 'update']);

    // Tudo que exige senha já trocada vai aqui dentro
    Route::middleware('password.changed')->group(function () {

        Route::view('dashboard', 'dashboard.index')->name('dashboard');

        /*
        |----------------------------------------------------------------------
        | Pessoas (módulo 02)
        |----------------------------------------------------------------------
        */
        Route::get('/pessoas', PessoaIndex::class)->name('pessoas.index');
        Route::get('/pessoas/novo', [PessoaController::class, 'create'])->name('pessoas.create');
        Route::get('/pessoas/{pessoa}/edit', [PessoaController::class, 'edit'])->name('pessoas.edit');
        Route::post('/pessoas', [PessoaController::class, 'store'])->name('pessoas.store');
        Route::put('/pessoas/{pessoa}', [PessoaController::class, 'update'])->name('pessoas.update');
        Route::patch('/pessoas/{pessoa}/toggle-status', [PessoaController::class, 'alternarStatus'])->name('pessoas.toggle-status');
        Route::delete('/pessoas/{pessoa}', [PessoaController::class, 'destroy'])->name('pessoas.destroy');

        Route::patch('/pessoas/{pessoa}/usuario', [PessoaController::class, 'atualizarUsuario'])->name('pessoas.atualizar-usuario');
        Route::patch('/pessoas/{pessoa}/usuario/revogar', [PessoaController::class, 'revogarUsuario'])->name('pessoas.revogar');
        Route::patch('/pessoas/{pessoa}/usuario/reativar', [PessoaController::class, 'reativarUsuario'])->name('pessoas.reativar');

        /*
        |----------------------------------------------------------------------
        | Papéis (módulo 03)
        |----------------------------------------------------------------------
        | Protegido pelo papel de sistema: o catálogo ainda não tem permissões
        | `papeis.*`, então quem controla o acesso é o papel em si. Quando essas
        | permissões entrarem no catálogo, troca-se `role:` por
        | `permission:papeis.visualizar` e o Gate::before passa a dar bypass ao
        | Admin automaticamente.
        |
        | ATENÇÃO: o middleware `role` é do Spatie e NÃO passa pelo Gate — ele
        | checa hasRole() direto. Por isso o Gate::before não interfere aqui.
        */
        Route::middleware('role:' . config('permissoes.papel_administrador'))
            ->prefix('papeis')
            ->name('papeis.')
            ->group(function () {
                // Listagem: componente Livewire full-page (busca, paginação).
                Route::get('/', PapeisIndex::class)->name('index');

                // Form: Blade + Controller. O grid de checkboxes é estado de UI
                // puro (Alpine), sem round-trip ao servidor a cada clique.
                Route::get('/criar', [PapelController::class, 'create'])->name('create');
                Route::post('/', [PapelController::class, 'store'])->name('store');
                Route::get('/{papel}/editar', [PapelController::class, 'edit'])->name('edit');
                Route::put('/{papel}', [PapelController::class, 'update'])->name('update');
                Route::delete('/{papel}', [PapelController::class, 'destroy'])->name('destroy');
            });
    });
});