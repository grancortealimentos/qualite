<?php

namespace App\Services;

use App\DTO\UsuarioData;
use App\Models\User;
use App\Repositories\UsuarioRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UsuarioService
{
    public function __construct(
        private readonly UsuarioRepository $usuarioRepository
    ) {}

    /**
     * Cria o usuário vinculado a uma pessoa.
     * 
     * Deve ser chamado DENTRO da transação de PessoaService, 
     * depois que a pessoa foi criada.
     * 
    */
    public function create(
        array $dadosValidados, 
        int $pessoaId, 
        string $nomeCompleto,
    ): User
    {
       return DB::transaction(function () use ($dadosValidados, $pessoaId, $nomeCompleto) {
            /**
             * Checagem defensiva dentro da transação.
             * A validação primária continua sendo o Rule::unique.
            */
            $email = $dadosValidados['usuario_email'];
            if($this->usuarioRepository->emailEmUso($email)) {
                throw ValidationException::withMessages([
                    'usuario_email' => 'Este e-mail já está em uso por outro usuário.'
                ]);
            }

            $data = UsuarioData::fromArray(
                $dadosValidados,
                $pessoaId,
                $nomeCompleto
            );

            return $this->usuarioRepository->create($data->toArray());
       });
    }

    /**
     * Revoga o acesso do usuário ao sistema
    */
    public function revogar(User $user): User
    {
        return DB::transaction(fn () => $this->usuarioRepository->revogar($user));
    }

    /**
     * Reativa o acesso do usuário ao sistema
    */
    public function reativar(User $user): User
    {
        return DB::transaction(fn () => $this->usuarioRepository->reativar($user));
    }
}