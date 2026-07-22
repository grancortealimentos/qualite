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

        require __DIR__ .'/pessoas.php';
        require __DIR__ .'/perfis.php';
        require __DIR__ .'/filiais.php';
    });
});