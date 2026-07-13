<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Services\Auth\ChangePasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChangePasswordController extends Controller
{
    public function __construct(
        private readonly ChangePasswordService $changePasswordService
    ) {}

    public function edit(): View
    {
        return view('auth.change-password');
    }

    public function update(ChangePasswordRequest $request): RedirectResponse
    {
        $this->changePasswordService->change($request->user(), $request->validated('password'));
        return redirect()->route('dashboard')->with('status', 'Senha alterada com sucesso');
    }
}