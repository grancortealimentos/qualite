<?php

namespace App\Repositories;

use App\Models\User;

class UsuarioRepository
{
    /**
     * Insere um novo usuário e retorna a model.
     * Recebe o array já montado pelo UsuarioData::toArray().
     * A senha vem texto puro; o cast 'hashed' no model faz o hash
    */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Retorna o usuário vinculado a uma pessoa, se existir.
     * Usa o escopo padrão de SoftDeletes (ignora deletados).
    */
    public function buscarPorPessoa(int $pessoaId): ?User
    {
        return User::where('pessoa_id', $pessoaId)->first();
    }

    /**
     * Verifica se um e-mail jpa estaá em uso por um usuário não deletado.
     * Útil como checagem defensiva dentro da transação, além do Rule::unique.
    */
    public function emailEmUso(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Revoga o acesso: desativa a conta;
     * Não deleta - a conta segue vinculada a pessoa e pode ser reativada.
    */
    public function revogar(User $user): User
    {
        $user->update([
            'is_active' => false
        ]);

        return $user->refresh();
    }

    /**
     * Reativa a conta do usuário previamente revogada.
    */
    public function reativar(User $user): User
    {
        $user->update([
            'is_active' => true
        ]);

        return $user->refresh();
    }
}