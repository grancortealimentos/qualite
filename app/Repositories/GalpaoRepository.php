<?php

namespace App\Repositories;

use App\DTO\GalpaoData;
use App\Models\Galpao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GalpaoRepository
{
    public function create(GalpaoData $data): Galpao
    {
        return Galpao::create($data->toArray());
    }

    public function update(Galpao $galpao, GalpaoData $data): Galpao
    {
        $galpao->update($data->toArray());
        return $galpao->fresh();
    }

    public function delete(Galpao $galpao): bool
    {
        return $galpao->delete();
    }

    public function buscarPorId(int $id): ?Galpao
    {
        return Galpao::with('propriedade')->find($id);
    }

    public function buscarPorPropriedade(int $propriedadeId): Collection
    {
        return Galpao::query()
            ->where('propriedade_id', $propriedadeId)
            ->ativos()
            ->orderBy('nome')
            ->get();
    }

    public function paginar(
        ?string $termo = null, 
        ?int $propriedadeId = null,
        int $porPagina = 10
    ): LengthAwarePaginator
    {
        $query = Galpao::query()->with('propriedade');
        if($propriedadeId !== null) {
            $query->where('propriedade_id', $propriedadeId);
        }

        if($termo !== null && $termo !== '') {
            $query->busca($termo);
        }

        return $query->orderBy('nome')->paginate($porPagina);
    }
}