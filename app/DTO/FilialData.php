<?php

namespace App\DTO;

final class FilialData
{
    public function __construct(
        public readonly string $razaoSocial,
        public readonly bool $ehAtivo,
        public readonly ?string $nomeFantasia = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $ie = null,
        public readonly ?string $cep = null,
        public readonly ?string $logradouro = null,
        public readonly ?string $numero = null,
        public readonly ?string $bairro = null,
        public readonly ?string $cidade = null,
        public readonly ?string $estado = null,
        public readonly ?string $pais = null,
        public readonly ?string $latitude = null,
        public readonly ?string $longitude = null,
    ) {}

    /**
     * Monta o DTO a partir dos dados já validados para criação.
    */
    public static function paraCriacao(array $data): self
    {
        return self::mapear($data);
    }

    /**
     * Monta o DTO a partir dos dados já validados para edição.
    */
    public static function paraEdicao(array $data): self
    {
        return self::mapear($data);
    }

    /**
     * Converte o DTO em array pronto para persistencia no Repository.
    */
    public function toArray(): array
    {
        return [
            'razao_social' => $this->razaoSocial,
            'eh_ativo' => $this->ehAtivo,
            'nome_fantasia' => $this->nomeFantasia,
            'cnpj' => $this->cnpj,
            'ie' => $this->ie,
            'cep' => $this->cep,
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'bairro' => $this->bairro,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'pais' => $this->pais,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    /**
     * Unico ponto de de-para array->DTO
    */
    private static function mapear(array $data): self
    {
        return new self(
            $data['razao_social'],
            $data['eh_ativo'] ?? true,
            $data['nome_fantasia'] ?? null,
            $data['cnpj'] ?? null,
            $data['ie'] ?? null,
            $data['cep'] ?? null,
            $data['logradouro'] ?? null,
            $data['numero'] ?? null,
            $data['bairro'] ?? null,
            $data['estado'] ?? null,
            $data['pais'] ?? null,
            $data['latitude'] ?? null,
            $data['longitude'] ?? null,
        );
    }
}