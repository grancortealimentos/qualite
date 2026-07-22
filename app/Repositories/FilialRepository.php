<?php

namespace App\Repositories;

use App\Models\Filial;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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
                fn($query) => $query->where('eh_ativo', $filtros['eh_ativo'])
            )
            ->when(
                !empty($filtros['busca']),
                function ($query) use ($filtros) {
                    $termo = $filtros['busca'];
                    $query->where(function ($sub) use ($termo) {
                        $sub->where('razao_social', 'ilike', "%{$termo}%")
                            ->orWhere('nome_fantasia', 'ilike', "%{$termo}%")
                            ->orWhere('cnpj', 'ilike', "%{$termo}%");
                    });
                }
            )
            ->when(
                !empty($filtros['cadastrado_de']),
                fn($query) => $query->whereDate('created_at', '>=', $filtros['cadastrado_de'])
            )
            ->orderBy('razao_social')
            ->paginate($porPagina);
    }

    /**
     * Busca uma filial pelo seu ID. Retorna null se não encontrar.
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
     * Persiste uma nova filial no banco.
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
     * Remove uma filial (soft deletes).
     */
    public function delete(Filial $filial): bool
    {
        return (bool) $filial->delete();
    }

    /**
     * Inverte o status ativo/inativo diretamente no banco (evita corrida).
     * Usa expressão SQL para não depender do valor lido previamente em memória.
     */
    public function alterarStatus(Filial $filial): bool
    {
        return (bool) $filial->update([
            'eh_ativo' => DB::raw('NOT eh_ativo'),
        ]);
    }

    /**
     * Verifica se já existe outra filial (não deletada) com o CNPJ informado.
     * ignorarId exclui a própria filial da checagem durante a edição.
     */
    public function existeCnpj(string $cnpj, ?int $ignorarId = null): bool
    {
        return Filial::query()
            ->where('cnpj', $cnpj)
            ->when($ignorarId, fn($query) => $query->where('id', '!=', $ignorarId))
            ->exists();
    }
}