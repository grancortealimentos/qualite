<?php

namespace App\Repositories;

use App\DTO\PapelData;
use App\Models\Papel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PapelRepository
{
    /**
     * Lista paginada para a tela de início.
     * 
     * withCount evita N+1: a listagem mostra a contagem de permissões e de
     * usuários de cada papel, e sem isso seria uma query por linha 
     * 
     * ilike é intencional - busca case-insensitive no PostgreSQL.
    */
    public function paginar(?string $busca = null, int $porPagina = 10): LengthAwarePaginator
    {
        return Papel::query()
            ->withCount(['permissions', 'users'])
            ->when($busca, fn ($q) => $q->where('name', 'ilike', "%{$busca}%"))
            ->orderBy('name')
            ->paginate($porPagina);
    }

    /**
     * Busca um papel pelo id, com as permissões já carregadas.
     * 
     * A tela de edição precisa saber quai checkboxes marcar, 
     * então o eager loading de permissions vem junto por padrão.
    */
    public function buscaPorId(int $id): ?Papel
    {
        return Papel::with('permissions')->find($id);
    }

    /**
     * Conta quantos usuários estão vinculados ao papel.
     * 
     * Usado pelo Service para bloquear a exclusão: apagar um papel
     * com usuários deixaria essas contas sem papel nenhum, violando o RN-012.
    */
    public function contarUsuarios(Papel $papel): int
    {
        return $papel->users()->count();
    }

    /**
     * Cria o papel.
     * 
     * Grava apenas as colunas da tabela 'roles'. O vinculo com as permissões
     * é responsabilidade dp Service, que chama sincronizarPermissões() dentro
     * da mesma transação.
    */
    public function create(PapelData $data): Papel
    {
        return Papel::create($data->toArrayParaBanco());
    }

    /**
     * Atualiza o nome do papel
    */
    public function update(Papel $papel, PapelData $data): Papel
    {
        $papel->update($data->toArrayParaBanco());
        return $papel;
    }

    /**
     * Exclui o papel.
     * 
     * Hard delete: a tabela 'roles' do Spatie não tem deleted_at.
     * É a exceção ao padrão de SoftDeletes do resto do projeto.
    */
    public function delete(Papel $papel): void
    {
        $papel->delete();
    }

    /**
     * Substitui as permissões do papel pelas informadas.
     * 
     * syncPermissions é destrutivo de propósito: apaga os vínculos antigos
     * em roles_has_permissions e grava só os novos. É o comportamento correto
     * para um form de checkboxes, onde caixa desmarcada = permissão removida.
    */
    public function sincronizarPermissoes(Papel $papel, array $permissoes): void
    {
        $papel->syncPermissions($permissoes);
    }

    /**
     * Verifica se já existe pape com este nome, igonorando maiusculas/minusculas.
     * 
     * ilike é o operador case-insensitive do Postgres.
     * Sem escape, os curingas %  e _ do LIKE seriam interpretados: 
     * um papel chamado "Gerent_" casaria com "Gerente" e o nome seria recusado
     * sem motivo aparente.
     * 
     * $ignorarId é usado na edição: sem ele, salvar o papel sem trocar o nome 
     * encontraria o proprio registro e acusaria duplicidade.
    */
    public function existeComNome(string $nome, ?int $ignorarId = null): bool
    {
        return Papel::query()
            ->where('guard_name', 'web')
            ->where('name', 'ilike', $this->escaparLike($nome))
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->exists();
    }

    /**
     * Escapa os curingas do LIKE/ILIKE para que o nome seja comparado literalmente,
     * e não como padrão de busca.
    */
    private function escaparLike(string $valor): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $valor);
    }
}