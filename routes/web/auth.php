<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LogoutController::class, 'store'])->name('logout');

    // Troca de senha: autenticada, mas SEM o middleware de bloqueio
    Route::get('alterar-senha', [ChangePasswordController::class, 'edit'])->name('password.change');
    Route::put('alterar-senha', [ChangePasswordController::class, 'update']);

    // Tudo que exige senha já trocada vai aqui dentro
    Route::middleware('password.changed')->group(function () {
        Route::view('dashboard', 'dashboard.index')->name('dashboard');
        // futuras telas internas entram aqui
    });
});