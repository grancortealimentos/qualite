<?php

use App\Http\Controllers\PapelController;
use App\Livewire\Papeis\Index as PapeisIndex;

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