<?php

namespace App\Services;

use App\DTO\PropriedadeData;
use App\Models\Propriedade;
use App\Repositories\PropriedadeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PropriedadeService
{
    public function __construct(
        private readonly PropriedadeRepository $propriedadeRepository
    ) {}

    /**
     * Cria uma nova propriedade.
     * Valida duplicidade de CNPJ e coerência das áreas antes de persistir.
    */
    public function create(PropriedadeData $data): Propriedade
    {
        $this->garantirCnpjUnico($data->cnpj);
        $this->garantirAreasCoerentes($data);

        return DB::transaction(function () use ($data) {
            return $this->propriedadeRepository->create($data);
        });
    }

    /**
     * Atualiza uma propriedade existente.
     * O próprio registro é ignorado na checagem de cnpj duplicado.
    */
    public function update(Propriedade $propriedade, PropriedadeData $data): Propriedade
    {
        \Log::debug('[SVC] update', [
            'propriedade_id' => $propriedade->id ?? 'NULL',
            'propriedade_exists' => $propriedade->exists,
            'cnpj_data' => $data->cnpj,
        ]);
        $this->garantirCnpjUnico($data->cnpj, $propriedade->id);
        $this->garantirAreasCoerentes($data);

        return DB::transaction(function () use ($propriedade, $data) {
            return $this->propriedadeRepository->update($propriedade, $data);
        });
    }

    /**
     * Ativa ou inativa a propriedade.
     * Inativa não exclui o registro: apenas remove
    */
    public function alterarStatus(Propriedade $propriedade, bool $ehAtivo): Propriedade
    {
        return DB::transaction(function () use ($propriedade, $ehAtivo) {
            return $this->propriedadeRepository->alterarStatus($propriedade, $ehAtivo);
        });
    }

    public function delete(Propriedade $propriedade): bool
    {
        return DB::transaction(function () use ($propriedade) {
            return $this->propriedadeRepository->delete($propriedade);
        });
    }

    // ------------------------------------------------------------------
    // Regras de negócio
    // ------------------------------------------------------------------

    /**
     * Impede o cadastro de duas propriedades com o mesmo CNPJ.
     * Lança ValidationException para que o erro volte ao formulário no campo correto.
    */
    private function garantirCnpjUnico(string $cnpj, ?int $ignorarId = null): void
    {
        $existente = $this->propriedadeRepository->buscarPorCnpj($cnpj, $ignorarId);
        if($existente !== null) {
            throw ValidationException::withMessages([
                'cnpj' => 'Já existe uma propriedade cadastrada com este CNPJ.'
            ]);
        }
    }

    /**
     * Garante que as áreas informais sejam coerentes com a área total:
     * nenhuma área parcial pode ser maior que a total, e a soma delas também não.
     * Só valida quando a área total foi informada.
    */
    private function garantirAreasCoerentes(PropriedadeData $data): void
    {
        if($data->areaTotal === null) {
            return;
        }

        $parciais = [
            'area_consolidada' => $data->areaConsolidada,
            'area_reservada_legal' => $data->areaReservadaLegal,
            'area_app' => $data->areaApp,
        ];

        foreach($parciais as $campo => $valor) {
            if($valor !== null && $valor > $data->areaTotal) {
                throw ValidationException::withMessages([
                    $campo => 'Esta área não pode ser maior que a área total da propriedade.',
                ]);
            }
        }

        $soma = array_sum(array_filter($parciais, fn($valor) => $valor !== null));
        if($soma > $data->areaTotal) {
            throw ValidationException::withMessages([
                'area_total' => 'A soma das áreas informadas ultrapassa a área total da propriedade.',
            ]);
        }
    }
}