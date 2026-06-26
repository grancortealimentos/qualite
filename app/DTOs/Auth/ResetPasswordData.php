<?php

namespace App\DTOs\Auth;

class ResetPasswordData
{
    public function __construct(
        public readonly string $email,
        public readonly string $token, 
        public readonly string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new  self(
            $data["email"],
            $data["token"],
            $data["password"]
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}