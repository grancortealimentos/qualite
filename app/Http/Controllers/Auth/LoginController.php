<?php

namespace App\Http\Controllers\Auth;

use App\DTO\LoginData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $this->authService->login(LoginData::fromArray([
            ...$request->validated(),
            'remember' => $request->boolean('remember'),
        ]));

        return redirect()->intended(route('dashboard'));
    }
}