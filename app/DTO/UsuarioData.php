<?php

namespace App\DTO;

class UsuarioData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,

        public readonly bool $isActive = true,
        public readonly bool $forcePasswordChange = true,

        public readonly ?int $pessoaId = null,
        public readonly ?string $password = null, 
        public readonly ?string $passwordResetExpiresAt = null,
    ) {}

    /**
     * Criação: pessoa_id e senha provisória são obrigatórios.
     * Admin decide force_password_change e data lmite de troca
    */
    public static function paraCriacao(
        array $dados, 
        int $pessoaId, 
        string $nomeCompleto
    ): self
    {
        return new self(
            $dados['usuario_name'] ?? $nomeCompleto,
            $dados['usuario_email'],
            true,
            $dados['usuario_force_password_change'] ?? true,
            $pessoaId,
            $dados['usuario_password'],
            $dados['usuario_password_expires_at'] ?? null,
        );
    }

    /**
     * Edição: nome, e-mail, forçar troca e data limite
     * Sem senha e sem pessoa_id (vínculo imutável)
    */
    public static function paraEdicao(array $dados): self
    {
        return new self(
            $dados['usuario_name'],
            $dados['usuario_email'],
            $dados['usuario_force_password_change'] ?? false,
            $dados['usuario_password_expires_at'] ?? null,
        );
    }

    
    public function toArray(): array
    {
        return [
            'pessoa_id' => $this->pessoaId,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'is_active' => $this->isActive,
            'force_password_change' => $this->forcePasswordChange,
            'password_reset_expires_at' => $this->passwordResetExpiresAt,
            'password_changed_at' => null,
        ];
    }

    public function toArrayParaUpdate(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'force_password_change' => $this->forcePasswordChange,
            'password_reset_expires_at' => $this->passwordResetExpiresAt,
        ]; 
    }
}