<?php

namespace App\DTO;

class BaiaData
{
    public function __construct(
        public readonly bool $ehAtivo,
        public readonly int $galpaoId,
        public readonly string $lote,
        public readonly ?string $descricao,
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
            galpaoId: (int) $data['galpao_id'],
            lote: $data['lote'],
            descricao: $data['descricao'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'eh_ativo' => $this->ehAtivo,
            'galpao_id' => $this->galpaoId,
            'lote' => $this->lote,
            'descricao' => $this->descricao,
        ];
    }
}