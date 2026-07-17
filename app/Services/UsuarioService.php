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
     * Cria usuário vinculado a uma pessoa.
     * 
     * Chamado dentro da transação do PessoaService. 
     * Como este método também abre DB::transaction, vira uma transação aninhada: 
     * se falhar, faz rollback ao savepoint e relança, derrubando a transação pai.
     * Se rodar isolado, se protege sozinho.
     * 
     * A admin define a senha provisória, se forçar a troca e a data limite;
     * O DTO carrega as decisões.
    */
    public function create(
        array $dadosValidados, 
        int $pessoaId, 
        string $nomeCompleto
    ): User
    {
        return DB::transaction(function () use ($dadosValidados, $pessoaId, $nomeCompleto) {
            
            //Checagem defensiva dentro da transação.
            //Validação primaria sendo Rule::unique
            $email = $dadosValidados['usuario_email'];
            if($this->usuarioRepository->emailEmUso($email)) {
                throw ValidationException::withMessages([
                    'usuario_email' => 'Este e-mail já está em uso por outro usuário.'
                ]);
            }

            $usuarioData = UsuarioData::paraCriacao(
                $dadosValidados,
                $pessoaId,
                $nomeCompleto,
            );

            $user = $this->usuarioRepository->create($usuarioData);
            $this->usuarioRepository->sincronizarPapel($user, $usuarioData->papelId);

            return $user;
        });
    }

    /**
     * Atualiza os dados de acesso pelo admin: nome, email, 
     * forçar troca de senha e data limite para a proxima troca.
    */
    public function update(User $user, array $dadosValidados): User
    {
        return DB::transaction(function () use ($user, $dadosValidados) {
            
            //ignora o proprio usuario, manter o mesmo e-mail não é conflito
            $email = $dadosValidados['usuario_email'];
            if($this->usuarioRepository->emailEmUso($email, $user->id)) {
                throw ValidationException::withMessages([
                    'usuario_email' => 'Este e-mail está em uso por outro usuário.'
                ]);
            }

            $usuarioData = UsuarioData::paraEdicao($dadosValidados);
            $user = $this->usuarioRepository->update($user, $usuarioData);
            $this->usuarioRepository->sincronizarPapel($user, $usuarioData->papelId);

            return $user;
        });
    }

    /**
     * Revoga acesso do usuário vinculado a pessoa.
    */
    public function revogar(User $user): User
    {
        return DB::transaction(fn () => $this->usuarioRepository->revogar($user));
    }

    /**
     * Reativa o acesso do usuário, previamente revogada.
    */
    public function reativar(User $user): User
    {
        return DB::transaction(fn () => $this->usuarioRepository->reativar($user));
    }
}