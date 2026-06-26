<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\Auth\RegisterUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = $this->authService->register(RegisterUserData::fromArray($request->validated()));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}