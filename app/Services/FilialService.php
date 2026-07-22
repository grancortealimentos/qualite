<?php

namespace App\Services;

use App\DTO\FilialData;
use App\Models\Filial;
use App\Repositories\FilialRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FilialService
{
    public function __construct(
        private readonly FilialRepository $filialRepository,
    ) {
    }

    /**
     * Lista filiais paginadas a partir dos filtros vindos da tela.
     * Traduz as chaves da UI (search/ativo/created_from) para o formato
     * que o Repository espera antes de paginar.
     */
    public function listar(array $filtros): LengthAwarePaginator
    {
        return $this->filialRepository->paginar(
            filtros: $this->traduzirFiltros(filtros: $filtros),
        );
    }

    public function create(FilialData $data): Filial
    {
        return DB::transaction(function () use ($data) {
            $this->validarCnpjUnico(cnpj: $data->cnpj);

            return $this->filialRepository->create(data: $data->toArray());
        });
    }

    public function update(Filial $filial, FilialData $data): Filial
    {
        return DB::transaction(function () use ($filial, $data) {
            // Valida o CNPJ ignorando a própria filial (não colide consigo mesma).
            $this->validarCnpjUnico(cnpj: $data->cnpj, ignorarId: $filial->id);

            return $this->filialRepository->update(
                filial: $filial,
                data: $data->toArray(),
            );
        });
    }

    public function delete(Filial $filial): void
    {
        DB::transaction(function () use ($filial) {
            $this->filialRepository->delete(filial: $filial);
        });
    }

    public function alterarStatus(Filial $filial): Filial
    {
        return DB::transaction(function () use ($filial) {
            $this->filialRepository->alterarStatus(filial: $filial);

            return $filial->refresh();
        });
    }

    /**
     * Garante que o CNPJ informado não pertence a outra filial.
     * Lança ValidationException (que sobe até o Controller) quando duplicado.
     */
    private function validarCnpjUnico(?string $cnpj, ?int $ignorarId = null): void
    {
        // CNPJ vazio não entra na regra de unicidade.
        if (empty($cnpj)) {
            return;
        }

        if ($this->filialRepository->existeCnpj(cnpj: $cnpj, ignorarId: $ignorarId)) {
            throw ValidationException::withMessages([
                'cnpj' => 'Já existe uma filial cadastrada com este CNPJ.',
            ]);
        }
    }

    /**
     * De-para dos filtros da UI para os filtros do Repository.
     * Centraliza a semântica do status tri-state e o filtro de data.
     */
    private function traduzirFiltros(array $filtros): array
    {
        $traduzidos = [];

        // Busca textual (razão social, nome fantasia, CNPJ).
        if (!empty($filtros['search'])) {
            $traduzidos['busca'] = $filtros['search'];
        }

        // Status: '' => só ativas (padrão); '0' => só inativas; 'todos' => sem filtro.
        $ativo = $filtros['ativo'] ?? '';
        if ($ativo === '') {
            $traduzidos['eh_ativo'] = true;
        } elseif ($ativo === '0') {
            $traduzidos['eh_ativo'] = false;
        }

        // Cadastradas a partir de uma data.
        if (!empty($filtros['created_from'])) {
            $traduzidos['cadastrado_de'] = $filtros['created_from'];
        }

        return $traduzidos;
    }
}