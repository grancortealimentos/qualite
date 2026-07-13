<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function store(): RedirectResponse
    {
        $this->authService->logout();
        return redirect()->route('login');
    }
}