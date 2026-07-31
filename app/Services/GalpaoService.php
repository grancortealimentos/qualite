<?php

namespace App\Services;

use App\DTO\GalpaoData;
use App\Models\Galpao;
use App\Models\Propriedade;
use App\Repositories\GalpaoRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GalpaoService
{
    public function __construct(
        private readonly GalpaoRepository $galpaoRepository
    ) {}

    public function create(GalpaoData $data): Galpao
    {
        $this->validarPropriedadeAtiva($data->propriedadeId);
        return DB::transaction(function () use ($data) {
            return $this->galpaoRepository->create($data);
        });
    }

    public function update(Galpao $galpao, GalpaoData $data): Galpao
    {
        $this->validarPropriedadeAtiva($data->propriedadeId);
        return DB::transaction(function () use ($galpao, $data) {
            return $this->galpaoRepository->update($galpao, $data);
        });
    }

    public function delete(Galpao $galpao): bool
    {
        if($galpao->baias()->ativas()->exists()) {
            throw ValidationException::withMessages([
                'galpao' => 'Não é possível excluir um galpao com baias ativas vinculadas.'
            ]);
        }

        return DB::transaction(function () use ($galpao) {
            return $this->galpaoRepository->delete($galpao);
        });
    }

    public function paginar(
        ?string $termo = null,
        ?int $propriedadeId = null,
        int $porPagina = 10
    )
    {
        return $this->galpaoRepository->paginar(
            $termo,
            $propriedadeId,
            $porPagina
        );
    }

    private function validarPropriedadeAtiva(int $propriedadeId): void
    {
        $propriedade = Propriedade::find($propriedadeId);
        if(!$propriedade || !$propriedade->eh_ativo) {
            throw ValidationException::withMessages([
                'propriedade_id' => 'A propriedade informada não existe ou está inativada',
            ]);
        }
    }
}