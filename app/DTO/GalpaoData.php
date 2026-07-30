<?php

namespace App\DTO;

class GalpaoData
{
    public function __construct(
        public readonly bool $ehAtivo,
        public readonly int $propriedadeId,
        public readonly string $tipo,
        public readonly string $nome,
    ) {}

    public function paraCriacao(array $data): self
    {
        return self::mapear($data);
    }

    public function paraEdicao(array $data): self
    {
        return self::mapear($data);
    }

    private static function mapear(array $data): self
    {
        return new self(
            ehAtivo: (bool) ($data['eh_ativo'] ?? true),
            propriedadeId: (int) $data['propriedade_id'],
            tipo: $data['tipo'],
            nome: $data['nome'],
        );
    }

    public function toArray(): array
    {
        return [
            'eh_ativo' => $this->ehAtivo,
            'propriedade_id' => $this->propriedadeId,
            'tipo' => $this->tipo,
            'nome' => $this->nome,
        ]; 
    }
}