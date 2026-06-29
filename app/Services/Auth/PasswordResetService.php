<?php

namespace App\Services\Auth;

use App\DTOs\Auth\ResetPasswordData;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetService
{
    public function sendResetLink(string $email): void
    {
        $status = Password::sendResetLink(['email' => $email]);
        if($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => __($status)
            ]);
        }
    }

    public function reset(ResetPasswordData $data): void
    {
        $status = Password::reset(
            $data->toArray(),
            function(User $user, string $password) {
                $user->forceFill(['password' => $password])->save();
                $user->setRememberToken(Str::random(60));
                event(new PasswordReset($user));
            }
        );

        if($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => __($status)
            ]);
        }
    }
}