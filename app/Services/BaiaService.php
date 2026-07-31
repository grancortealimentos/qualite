<?php

namespace App\Services;

use App\DTO\BaiaData;
use App\Models\Baia;
use App\Models\Galpao;
use App\Repositories\BaiaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BaiaService
{
    public function __construct(
        private readonly BaiaRepository $baiaRepository
    ) {}

    public function create(BaiaData $data): Baia
    {
        $this->validarGalpaoAtivo($data->galpaoId);
        return DB::transaction(function () use ($data) {
            return $this->baiaRepository->create($data);
        });
    }

    public function update(Baia $baia, BaiaData $data): Baia
    {
        $this->validarGalpaoAtivo($data->galpaoId);
        return DB::transaction(function () use ($baia, $data) {
            return $this->baiaRepository->update($baia, $data);
        });
    }

    public function delete(Baia $baia): bool 
    {
        return DB::transaction(function () use ($baia) {
            return $this->baiaRepository->delete($baia);
        });
    }

    public function paginar(
        ?string $termo = null,
        ?int $galpaoId = null,
        int $porPagina = 10
    )
    {
        return $this->baiaRepository->paginar(
            $termo,
            $galpaoId,
            $porPagina
        );
    }

    private function validarGalpaoAtivo(int $galpaoId): void
    {
        $galpao = Galpao::find($galpaoId);
        if(!$galpao || !$galpao->eh_ativo) {
            throw ValidationException::withMessages([
                'galpao_id' => 'O galpão informado não existe ou está inativado.'
            ]);
        }
    }
}