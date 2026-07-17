<?php

namespace App\Services;

use App\DTO\PapelData;
use App\Models\Papel;
use App\Repositories\PapelRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar;

class PapelService
{
    public function __construct(
        private readonly PapelRepository $papelRepository
    ) {}

    /**
     * Cria o papel e vincula suas permissões.
     * 
     * A transação não é opcional mesmo parecendo um write só: são três escritas
     * INSERT em 'roles', INSERT em 'role_has_permissions' (via syncPermissions)
     * e INSERT em 'auditorias' (via trait Auditavel). Sem transação, uma falha 
     * no meio deixaria papel criado sem permissão, ou papel gravado sem trilha
     * de auditoria.
    */
    public function create(PapelData $data): Papel
    {
        $papel = DB::transaction(function () use ($data) {
            $papel = $this->papelRepository->create($data);
            $this->papelRepository->sincronizarPermissoes($papel, $data->permissoes);

            return $papel;
        });

        $this->limparCache();
        return $papel;
    }

    /**
     * Atualiza o nome e permissões do papel.
     * 
     * Bloqueia o papel de sistema: renomear Admin quebraria o Gate::before
     * que compara pelo nome vindo do config. O sistema inteiro perderia o
     * bypass e ninguém mais entraria em lugar algum.
    */
    public function update(Papel $papel, PapelData $data): Papel
    {
        $this->garantirQueNaoEhPapelSistema($papel);
        $papel = DB::transaction(function () use ($papel, $data) {
            $papel = $this->papelRepository->update($papel, $data);
            $this->papelRepository->sincronizarPermissoes($papel, $data->permissoes);
            return $papel;
        });

        $this->limparCache();
        return $papel;
    }

    /**
     * Exclui o papel.
     * 
     * Dois bloqueios:
     *  1. Papel de sistema nunca é excluído;
     *  2. Papel com usuários vinculados não pode ser excluído:
     *  o Spatie faz cascade em model_has_roles, e essas contas ficaria
     * SEM papel nenhum violando o RN-012 (todo usuário tem exatamente 1 papel).
    */
    public function delete(Papel $papel): void
    {
        $this->garantirQueNaoEhPapelSistema($papel);
        $totalUsuarios = $this->papelRepository->contarUsuarios($papel);
        if($totalUsuarios > 0) {
            throw ValidationException::withMessages([
                'papel' => "Não é possível excluir: {$totalUsuarios} usuário(s) usa(m) este papel. Altere o papel dessas contas antes. "
            ]);
        }

        DB::transaction(function () use ($papel) {
            $this->papelRepository->delete($papel);
        });

        $this->limparCache();
    }

    /**
     * Barra qualquer mutação no papel de sistema (Admin).
    */
    private function garantirQueNaoEhPapelSistema(Papel $papel): void
    {
        if($papel->ehPapelSistema()) {
            throw ValidationException::withMessages([
                'papel' => 'O papel de sistema não pode ser alterado ou excluído.'
            ]);
        }
    }

    /**
     * Invalida o cache de permissões do Spatie.
     * 
     * Obrigatório após qualquer mudança em papel ou vinculo de permissão:
     * o pacote guarda o mapa em cache por 24h então sem isso o admin salva a tela, vê  
     * "savo com sucesso" e a permissão continua não valendo.
     * É a pegadinha clássica do Spatie - parece bug de gravação, mas é cache.
     * 
     * Fica de fora da transação de proposito: cache não faz rollback,
     * e limpar antes do commit abriria uma janela onde o cache já foi invalidado
     * mas o dado novo ainda não existe.
    */
    private function limparCache(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}