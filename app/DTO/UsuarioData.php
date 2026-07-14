<?php

namespace App\DTO;

class UsuarioData
{
    public function __construct(
        public readonly int $pessoaId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly bool $isActive = true,
        public readonly bool $forcePasswordChange = true,
    ) {}

    /**
     * Cria o DTO a partir dos dados já validados no PessoaRequest.
     * O 'name' cai para o nome_completo da pessoa quado não informado.
    */
    public static function fromArray(
        array $dados, 
        int $pessoaId, 
        string $nomeCompleto
    ): self
    {
        return new self(
            $pessoaId,
            $dados['usuario_name'] ?? $nomeCompleto,
            $dados['usuario_email'],
            $dados['usuario_password'],
            $dados['usuario_is_active'] ?? true,
            true,
        );
    }

    /**
     * Array para INSERT.
     * Senha em texto puro de proposito: o cast 'hashed' no model User faz hash.
    */
    public function toArray(): array
    {
        return [
            'pessoa_id' => $this->pessoaId,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'is_active' => $this->isActive,
            'force_password_change' => $this->forcePasswordChange,
            'password_changed_at' => null,
        ];
    }
}