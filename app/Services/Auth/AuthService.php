<?php

namespace App\Services\Auth;

use App\DTO\LoginData;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AuditoriaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuditoriaService $auditoria
    ) {}

    /**
     * RN-001: login exlusivamente com email e senha;
     * RN-002: só autentica se o usuário estiver ativo e a pessoa vinculada.
    */
    public function login(LoginData $data): User
    {
        $user = $this->userRepository->findByEmail($data->email);
        if(!$user || !Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if(!$user->is_active || !$user->pessoa?->eh_ativo) {
            throw ValidationException::withMessages([
                'email' => 'Sua conta está inativa. Entre em contato com o administrador.',
            ]);
        }

        Auth::login($user, $data->remember);

        request()->session()->regenerate();

        $this->auditoria->registrar(
            'login',
            'User',
            (string) $user->id,
            null,
            null,
            'Login realizado com sucesso.',
            $user->id,
        );

        return $user;
    }

    public function logout(): void
    {
        $user = Auth::user();
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        if($user) {
            $this->auditoria->registrar(
                'logout',
                'User',
                (string) $user->id,
                null,
                null,
                'logout realizado',
                $user->id
            );
        }
    } 
}