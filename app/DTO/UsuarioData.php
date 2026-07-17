<?php

namespace App\DTO;

class UsuarioData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,

        // Papel do usuário (RN-012: toda conta tem exatamente 1 papel).
        // Fica entre os obrigatórios de propósito: parâmetro sem default
        // depois de parâmetros com default é deprecado no PHP 8.1+.
        //
        // NÃO é coluna de `users` — não aparece em toArray() nem em
        // toArrayParaUpdate(). Vai para model_has_roles via syncRoles(),
        // que é trabalho do PessoaService.
        public readonly int $papelId,

        public readonly bool $isActive = true,
        public readonly bool $forcePasswordChange = true,

        public readonly ?int $pessoaId = null,
        public readonly ?string $password = null,
        public readonly ?string $passwordResetExpiresAt = null,
    ) {}

    /**
     * Criação: pessoa_id, senha provisória e papel são obrigatórios.
     * Admin decide force_password_change e data limite de troca.
     */
    public static function paraCriacao(
        array $dados,
        int $pessoaId,
        string $nomeCompleto
    ): self {
        return new self(
            name: $dados['usuario_name'] ?? $nomeCompleto,
            email: $dados['usuario_email'],
            papelId: (int) $dados['usuario_papel_id'],
            isActive: true,
            forcePasswordChange: $dados['usuario_force_password_change'] ?? true,
            pessoaId: $pessoaId,
            password: $dados['usuario_password'],
            passwordResetExpiresAt: $dados['usuario_password_expires_at'] ?? null,
        );
    }

    /**
     * Edição: nome, e-mail, papel, forçar troca e data limite.
     * Sem senha e sem pessoa_id (vínculo imutável).
     *
     * O papel entra aqui também: trocar de papel é edição comum (promoção,
     * mudança de função) e não passa por revogar/reativar.
     */
    public static function paraEdicao(array $dados): self
    {
        return new self(
            name: $dados['usuario_name'],
            email: $dados['usuario_email'],
            papelId: (int) $dados['usuario_papel_id'],
            forcePasswordChange: $dados['usuario_force_password_change'] ?? false,
            passwordResetExpiresAt: $dados['usuario_password_expires_at'] ?? null,
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