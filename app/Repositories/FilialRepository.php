<?php

namespace App\Repositories;

use App\Models\Filial;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class FilialRepository
{
    /**
     * Retorna uma listagem paginada de filiais, aplicando filtros opcionais.
    */
    public function paginar(array $filtros = [], int $porPagina = 10): LengthAwarePaginator
    {
        return Filial::query()
            ->when(
                isset($filtros['eh_ativo']),
                fn ($query) => $query->where('eh_ativo', $filtros['eh_ativo'])
            )
            ->when(
                !empty($filtros['busca']),
                function($query) use ($filtros) {
                    $termo = $filtros['busca'];
                    $query->where(function($sub) use ($termo) {
                        $sub->where('razao_social', 'ilike', "%{$termo}%")
                            ->orWhere('nome_fantasia', 'ilike', "%{$termo}%")
                            ->orWhere('cnpj', 'ilike', "%{$termo}%");
                    });
                }
            )
            ->orderBy('razao_social')
            ->paginate($porPagina);
    }

    /**
     * Busca uma filial pelo sei ID. Retorna null  se não encontrar.
    */
    public function buscarPorId(int $id): ?Filial
    {
        return Filial::find($id);
    }

    /**
     * Busca uma filial pelo ID ou lança ModelNotFoundException.
    */
    public function buscarPorIdOuFalhar(int $id): Filial
    {
        return Filial::findOrFail($id);
    }

    /**
     * Persiste uma nova filial no banco
    */
    public function create(array $data): Filial
    {
        return Filial::create($data);
    }

    /**
     * Atualiza uma filial existente e devolve o model com estado persistido.
    */
    public function update(Filial $filial, array $data): Filial
    {
        $filial->update($data);
        return $filial->refresh();
    }

    /**
     * Remove uma filial (soft deletes)
    */
    public function delete(Filial $filial): bool 
    {
        return (bool) $filial->delete();
    }
}