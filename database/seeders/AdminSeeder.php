<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'informatica@grancorte.com.br';
 
        $pessoa = Pessoa::firstOrCreate(
            ['email' => $email],
            [
                'eh_ativo' => true,
                'tipo_cadastro' => 'administrador',
                'nome_completo' => 'Administrador do Sistema',
                'pais' => 'Brasil',
            ]
        );
 
        User::firstOrCreate(
            ['email' => $email],
            [
                'pessoa_id' => $pessoa->id,
                'is_active' => true,
                'name' => 'Administrador do Sistema',
                'password' => 'CbU*723"', // texto puro — cast 'hashed' do model User faz o hash
                'force_password_change' => false, // ver observação: pode ser true se preferir forçar troca no 1º login
            ]
        );
    }
}
