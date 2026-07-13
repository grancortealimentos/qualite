<?php

namespace App\Repositories;

use App\DTO\PessoaData;
use App\Models\Pessoa;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PessoaRepository 
{
    public function create(PessoaData $data): Pessoa
    {
        return Pessoa::create($data->toArray());
    }

    public function update(Pessoa $pessoa, PessoaData $data): Pessoa
    {
        //toArrayParaUpdate() preserva a foto atual quando nenhuma nova é enviada
        $pessoa->update($data->toArrayParaUpdate());

        return $pessoa->refresh();
    }

    public function findById(int $id): ?Pessoa
    {
        return Pessoa::find($id);
    }

    public function delete(Pessoa $pessoa): bool
    {
        return (bool) $pessoa->delete();
    }

    public function alterarStatus(Pessoa $pessoa): Pessoa
    {
        $pessoa->update(['eh_ativo' => !$pessoa->eh_ativo]);

        return $pessoa->refresh();
    }

    public function paginar(array $filtros = [], int $porPagina = 10): LengthAwarePaginator
    {
        $busca = $filtros['search'] ?? null;
        $ativo = $filtros['ativo'] ?? null;
        $cadastradoEm = $filtros['created_from'] ?? null;
        $buscaNumerica = $busca ? preg_replace('/\D/', '', $busca) : null;

        return Pessoa::query()
            ->when(
                filled($busca),
                function ($query) use ($busca, $buscaNumerica) {
                    $query->where(function ($q) use ($busca, $buscaNumerica) {
                        $q->where('nome_completo', 'ilike', "%{$busca}%")
                            ->orWhere('email', 'ilike', "%{$busca}%");

                        if($buscaNumerica) {
                            $q->orWhere('documento', 'like', "%{$buscaNumerica}%");
                        }
                    });
                }
            )
            ->unless(
                $ativo === 'todos',
                fn($query) => $query->where('eh_ativo', $ativo !== '0')
            )
            ->when(
                filled($cadastradoEm),
                fn($query) => $query->whereDate('created_at', '>=', $cadastradoEm)
            )
            ->latest()
            ->paginate($porPagina)
            ->withQueryString();
    }
}