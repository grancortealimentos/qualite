<?php

namespace App\Repositories;

use App\DTO\PropriedadeData;
use App\Models\Propriedade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PropriedadeRepository
{
    public function paginar(array $filtros = [], int $porPagina = 10): LengthAwarePaginator
    {
        return Propriedade::query()
            ->with('produtor:id,nome_completo')
            ->busca($filtros['busca'] ?? null)
            ->when(
                isset($filtros['eh_ativo']) && $filtros['eh_ativo'] !== '',
                fn ($query) => $query->where('eh_ativo', filter_var($filtros['eh_ativo'], FILTER_VALIDATE_BOOLEAN))
            )
            ->when(
                filled($filtros['produtor_id'] ?? null),
                fn ($query) => $query->where('produtor_id', $filtros['produtor_id'])
            )
            ->orderBy('razao_social')
            ->paginate($porPagina);
    }

    public function listarAtivas(): Collection
    {
        return Propriedade::query()
            ->ativas()
            ->orderBy('razao_social')
            ->get(['id', 'razao_social', 'nome_fantasia']);
    }

    public function buscarPorId(int $id): ?Propriedade
    {
        return Propriedade::query()->find($id);
    }

    public function buscarPorCnpj(string $cnpj, ?int $ignorarId = null): ?Propriedade
    {
        return Propriedade::query()
            ->where('cnpj', $cnpj)
            ->when($ignorarId, fn ($query) => $query->whereKeyNot($ignorarId))
            ->first();
    }

    public function buscarPorProdutor(int $produtorId): Collection
    {
        return Propriedade::query()
            ->where('produtor_id', $produtorId)
            ->orderBy('razao_social')
            ->get();
    }

    public function create(PropriedadeData $data): Propriedade
    {
        return Propriedade::query()->create($data->toArray());
    }

    public function update(Propriedade $propriedade, PropriedadeData $data): Propriedade
    {
        $propriedade->update($data->toArray());
        return $propriedade->refresh();
    }

    public function alterarStatus(Propriedade $propriedade, bool $ehAtivo): Propriedade
    {
        $propriedade->update(['eh_ativo' => $ehAtivo]);
        return $propriedade->refresh();
    }

    public function delete(Propriedade $propriedade): bool
    {
        return (bool) $propriedade->delete();
    }
}