<?php

namespace App\Livewire\Pessoas;

use App\Models\Pessoa;
use App\Services\PessoaService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Pessoas')]
class Index extends Component
{
    use WithPagination;

    /**
     * Argumentos NOMEADOS: o 2º parâmetro posicional de #[Url] é $history,
     * não $except. #[Url('search', '')] silenciosamente ligaria o history.
     */
    #[Url(as: 'search', except: '')]
    public string $busca = '';

    /**
     * ''      => somente ativos (padrão)
     * '0'     => somente inativos
     * 'todos' => ativos e inativos
     */
    #[Url(as: 'ativo', except: '')]
    public string $filtroAtivo = '';

    #[Url(as: 'created_from', except: '')]
    public string $cadastradoDe = '';

    /**
     * Tipo de cadastro (select único): '' => todos os tipos;
     * caso contrário, um dos valores de tipo_cadastro (Funcionario, Veterinario, Advogado, Produtor).
     */
    #[Url(as: 'tipo', except: '')]
    public string $filtroTipo = '';

    public bool $filtrosAbertos = false;

    /**
     * Ao montar, o card de filtros avançados já abre expandido
     * se algum filtro avançado estiver ativo (ex.: veio de um link com querystring).
     */
    public function mount(): void
    {
        $this->filtrosAbertos = $this->temFiltroAvancado();
    }

    // ---------- Reset de paginação ----------
    // O nome do hook precisa ser updated + NomeExatoDaPropriedade.
    // Um typo aqui não gera erro: o método simplesmente nunca é chamado.

    /**
     * Ao digitar na busca, volta para a primeira página.
     */
    public function updatedBusca(): void
    {
        $this->resetPage();
    }

    /**
     * Ao trocar o filtro de status, volta para a primeira página.
     */
    public function updatedFiltroAtivo(): void
    {
        $this->resetPage();
    }

    /**
     * Ao trocar a data de cadastro, volta para a primeira página.
     */
    public function updatedCadastradoDe(): void
    {
        $this->resetPage();
    }

    /**
     * Ao trocar o tipo de cadastro, volta para a primeira página.
     */
    public function updatedFiltroTipo(): void
    {
        $this->resetPage();
    }

    // ---------- Helpers de estado ----------

    /**
     * Indica se algum filtro do card "avançado" está ativo
     * (status, data de cadastro ou tipo de cadastro).
     */
    public function temFiltroAvancado(): bool
    {
        return $this->filtroAtivo !== '' || $this->cadastradoDe !== '' || $this->filtroTipo !== '';
    }

    /**
     * Indica se existe QUALQUER filtro ativo, incluindo a busca textual.
     * Usado para exibir os chips e o botão "Limpar filtros".
     */
    public function temQualquerFiltro(): bool
    {
        return $this->temFiltroAvancado() || $this->busca !== '';
    }

    /**
     * Reseta todos os filtros para o estado padrão e volta para a primeira página.
     */
    public function limparFiltros(): void
    {
        $this->reset(['busca', 'filtroAtivo', 'cadastradoDe', 'filtroTipo']);
        $this->resetPage();
    }

    // ---------- Ações ----------

    /**
     * Ativa/desativa uma pessoa, checando permissão e policy antes de delegar ao service.
     */
    public function alternarStatus(int $pessoaId, PessoaService $pessoaService): void
    {
        $pessoa = Pessoa::findOrFail($pessoaId);

        if (!auth()->user()->can('pessoas.status') || !auth()->user()->can('alterarStatus', $pessoa)) {
            // Argumentos NOMEADOS: viram as chaves de $event.detail no Alpine.
            $this->dispatch('toast', tipo: 'error', mensagem: 'Você não tem permissão para alterar o status.');
            return;
        }

        $pessoa = $pessoaService->alternarStatus($pessoa);

        $this->dispatch(
            'toast',
            tipo: 'success',
            mensagem: $pessoa->eh_ativo ? 'Pessoa ativada.' : 'Pessoa desativada.'
        );
    }

    /**
     * Exclui (soft delete) uma pessoa, checando permissão e policy antes de delegar ao service.
     */
    public function excluir(int $pessoaId, PessoaService $pessoaService): void
    {
        $pessoa = Pessoa::findOrFail($pessoaId);

        if (!auth()->user()->can('pessoas.excluir') || !auth()->user()->can('delete', $pessoa)) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Você não tem permissão para excluir a pessoa.');
            return;
        }

        $pessoaService->delete($pessoa);

        $this->dispatch('toast', tipo: 'success', mensagem: 'Pessoa excluída com sucesso.');
    }

    // ---------- Render ----------

    /**
     * O service é injetado pelo container. A view PRECISA receber $pessoas.
     */
    public function render(PessoaService $pessoaService)
    {
        $pessoas = $pessoaService->listar([
            'search'        => $this->busca,
            'ativo'         => $this->filtroAtivo,
            'created_from'  => $this->cadastradoDe,
            'tipo_cadastro' => $this->filtroTipo,
        ]);

        return view('livewire.pessoas.index', compact('pessoas'));
    }
}