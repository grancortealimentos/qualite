<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class ChangePasswordService
{
    public function change(User $user, string $newPassword): void
    {
        //Regra 1: não pode ser igual a senha atual
        if(Hash::check($newPassword, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'A nova senha não pode ser igual à senha atual.'
            ]);
        }

        //RN-008: não pode ser igual a anterior
        if($user->previous_password && Hash::check($newPassword, $user->previous_password)) {
            throw ValidationException::withMessages([
                'password' => 'A nova senha não pode ser igual a anterior.',
            ]);
        }

        //RN-009: Hash anterior -> previous_password, password atualizado,
        //password_change_at atual, force_password_change desativado
        $user->forceFill([
            'previous_password' => $user->password,
            'password' => Hash::make($newPassword),
            'password_changed_at' => now(),
            'force_password_change' => false,
        ])->save();
    }
}