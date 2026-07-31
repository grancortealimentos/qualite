<?php

namespace App\Repositories;

use App\DTO\BaiaData;
use App\Models\Baia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BaiaRepository
{
    public function create(BaiaData $data): Baia
    {
        return Baia::create($data->toArray());
    }

    public function update(Baia $baia, BaiaData $data): Baia
    {
        $baia->update($data->toArray());
        return $baia->fresh();
    }

    public function delete(Baia $baia): bool
    {
        return $baia->delete();
    }

    public function buscarPorId(int $id): ?Baia
    {
        return Baia::with('galpao')->find($id);
    }

    public function buscarPorGalpao(int $galpaoId): Collection
    {
        return Baia::query()
            ->where('galpao_id', $galpaoId)
            ->ativas()
            ->orderBy('lote')
            ->get();
    }

    public function paginar(
        ?string $termo = null,
        ?int $galpaoId = null,
        int $porPagina = 10
    ): LengthAwarePaginator
    {
        $query = Baia::query()->with('galpao');
        if($galpaoId !== null) {
            $query->where('galpao_id', $galpaoId);
        }

        if($termo !== null && $termo !== '') {
            $query->busca($termo);
        }

        return $query->orderBy('lote')->paginate($porPagina);
    }
}