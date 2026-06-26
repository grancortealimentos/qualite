<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\Auth\LoginData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $this->authService->login(LoginData::fromArray([
            ...$request->validated(),
            'remember' => $request->boolean('remember'),
        ]));

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}