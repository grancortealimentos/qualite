<?php

namespace App\DTO;

/**
 * Carrega os dados já validados do PapelRequest até o Service/Repository
 * sem que essas camadas precisem conhecer o objeto request.
*/
class PapelData
{
    public function __construct(
        public readonly string $nome,
        public readonly array $permissoes = []
    ) {}

    /**
     * Monta o DTO a partir do array já validado do PapelRequest.
    */
    public static function fromArray(array $dados): self
    {
        return new self(
            nome: $dados['nome'],
            permissoes: $dados['permissoes'] ?? [],
        );
    }

    /**
     * Devolve apenas o que é coluna da tabela 'roles'.
    */
    public function toArrayParaBanco(): array
    {
        return [
            'name' => $this->nome,
            'guard_name' => 'web'
        ];
    }
}