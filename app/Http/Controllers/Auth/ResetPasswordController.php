<?php

namespace App\Http\Controllers\Auth;

use App\DTO\ResetPasswordData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function __construct(
        private readonly PasswordResetService $passwordResetService
    ) {}

    public function create(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email')
        ]); 
    }

    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $this->passwordResetService->reset(ResetPasswordData::fromArray($request->validated()));

        return redirect()->route('login')->with('status', __('passwords.reset'));
    }
}