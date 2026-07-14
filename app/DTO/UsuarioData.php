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

    
}