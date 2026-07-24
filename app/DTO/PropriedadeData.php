<?php

namespace App\DTO;

final class PropriedadeData
{
    public function __construct(
        public readonly int $produtorId,
        public readonly bool $ehAtivo,
        public readonly string $razaoSocial,
        public readonly ?string $nomeFantasia,
        public readonly string $cnpj,
        public readonly ?string $ie,
        public readonly ?string $nrif,
        public readonly ?string $car,
        public readonly ?float $areaTotal,
        public readonly ?float $areaConsolidada,
        public readonly ?float $areaReservadaLegal,
        public readonly ?float $areaApp,
        public readonly ?int $capacidadeArmazenamento,
        public readonly ?string $cep,
        public readonly ?string $logradouro,
        public readonly ?string $numero,
        public readonly ?string $bairro,
        public readonly ?string $cidade,
        public readonly ?string $estado,
        public readonly ?string $pais,
        public readonly ?string $complemento,
        public readonly ?string $latitude,
        public readonly ?string $longitude,
    ) {}

    /**
     * Monta o DTO a partir dos dados validados de criação.
    */
    public static function paraCriacao(array $data): self
    {
        return self::mapear($data);
    }

    /**
     * Monta o DTO a partir dos dados validados de edição.
    */
    public static function paraEdicao(array $data): self
    {
        return self::mapear($data);
    }

    /**
     * De-para array -> DTO
    */
    private static function mapear(array $data): self
    {
        return new self(
            produtorId: (int) $data['produtor_id'],
            ehAtivo: (bool) ($data['eh_ativo'] ?? true),
            razaoSocial: $data['razao_social'],
            nomeFantasia: $data['nome_fantasia'] ?? null,
            cnpj: $data['cnpj'],
            ie: $data['ie'] ?? null,
            nrif: $data['nrif'] ?? null,
            car: $data['car'] ?? null,
            areaTotal: isset($data['area_total']) ? (float) $data['area_total'] : null,
            areaConsolidada: isset($data['area_consolidada']) ? (float) $data['area_consolidada'] : null,
            areaReservadaLegal: isset($data['area_reservada_legal']) ? (float) $data['area_reservada_legal'] : null,
            areaApp: isset($data['area_app']) ? (float) $data['area_app'] : null,
            capacidadeArmazenamento: isset($data['capacidade_armazenamento']) ? (int) $data['capacidade_armazenamento'] : null,
            cep: $data['cep'] ?? null,
            logradouro: $data['logradouro'] ?? null,
            numero: $data['numero'] ?? null,
            bairro: $data['bairro'] ?? null,
            cidade: $data['cidade'] ?? null,
            estado: $data['estado'] ?? null,
            pais: $data['pais'] ?? null,
            complemento: $data['complemento'] ?? null,
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
        );
    }

    /**
     * Saída canônica para o Repository
    */
    public function toArray(): array
    {
        return [
            'produtor_id' => $this->produtorId,
            'eh_ativo' => $this->ehAtivo,
            'razao_social' => $this->razaoSocial,
            'nome_fantasia' => $this->nomeFantasia,
            'cnpj' => $this->cnpj,
            'ie' => $this->ie,
            'nrif' => $this->nrif,
            'car' => $this->car,
            'area_total' => $this->areaTotal,
            'area_consolidada' => $this->areaConsolidada,
            'area_reservada_legal' => $this->areaReservadaLegal,
            'area_app' => $this->areaApp,
            'capacidade_armazenamento' => $this->capacidadeArmazenamento,
            'cep' => $this->cep, 
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'bairro' => $this->bairro,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'pais' => $this->pais, 
            'complemento' => $this->complemento,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}