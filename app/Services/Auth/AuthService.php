<?php

namespace App\Services\Auth;

use App\DTOs\Auth\LoginData;
use App\DTOs\Auth\RegisterUserData;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function register(RegisterUserData $data): User
    {
        return $this->userRepository->create($data->toArray());
    }

    public function login(LoginData $data): User
    {
        $credentials = [
            'email' => $data->email,
            'password' => $data->password
        ];

        if(!Auth::attempt($credentials, $data->remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return Auth::user();
    }

    public function logout(): void
    {
        Auth::logout();
    }
}