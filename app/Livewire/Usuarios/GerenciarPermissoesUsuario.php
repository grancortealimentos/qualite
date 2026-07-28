<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use App\Services\UsuarioService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Permissões de Usuário')]
class GerenciarPermissoesUsuario extends Component
{
    //usuario-alvo.
    public User $usuario;

    /**
     * Nome das permissões que vem dos papeis do usuário.
     * São exibidas marcadas e travadas - não se edita permissão do papel por aqui.
    */
    public array $permissoesDoPapel = [];

    /**
     * Nomes das permissões DIRETAS (tabela model_has_permissions)
     * É o único conjunto que os checkboxes editáveis controlam.
    */
    public array $permissoesDiretas = [];

    /**
     * Carrega estado inicial separando as duas origens de permissão do Spatie:
     * - getPermissionsViaRoles(): tudo herdado dos papeis (fica travado aqui);
     * - getDirectPermissions(): o que já foi concedido direto no usuário (editável)
    */
    public function mount(User $usuario): void
    {
        $this->usuario = $usuario;
        $this->permissoesDoPapel = $usuario->getPermissionsViaRoles()
            ->pluck('name')
            ->all();

        $this->permissoesDiretas = $usuario->getDirectPermissions()
            ->pluck('name')
            ->all();
    }

    /**
     * Diz se uma permissão vem do papel - usado no blade para marcar como 
     * checked + disabled. Se vem do perfil, o checkbox direto fica travado
    */
    public function estaNoPapel(string $permissao): bool
    {
        return in_array($permissao, $this->permissoesDoPapel, true);
    }

    public function salvar(array $permissoes, UsuarioService $usuarioService): void
    {
        if(!auth()->user()->can('usuarios.gerenciar_permissoes')) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Você não tem permissão para gerenciar permissões.');
            return; // sem isto, seguia salvando mesmo sem permissão
        }

        $usuarioService->sincronizarPermissoesDiretas($this->usuario, $permissoes);

        // Reseeda a baseline pra um próximo salvar comparar contra o estado real.
        $this->usuario->unsetRelation('permissions');
        $this->permissoesDiretas = $this->usuario->getDirectPermissions()->pluck('name')->all();

        $this->dispatch('toast', tipo: 'success', mensagem: 'Permissões atualizadas com sucesso.');
    }

    public function render()
    {
        return view('livewire.usuarios.gerenciar-permissoes-usuario', [
            'grupos' => config('permissoes.grupos'),
        ]);
    }
}
