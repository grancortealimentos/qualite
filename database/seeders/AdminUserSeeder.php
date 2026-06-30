<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'informatica@grancorte.com.br'],
            [
                'name' => 'Administrador',
                'password' => 'CbU*723"',          
                'password_changed_at' => now(),          
                'force_password_change' => false,        
            ],
        );
    }
}
