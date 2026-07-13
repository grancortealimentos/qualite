<?php

namespace App\Repositories;

use App\Models\User;

final class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::query()->with('pessoa')->where('email', $email)->first();
    }
}