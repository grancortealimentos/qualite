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

    public bool $filtrosAbertos = false;

    public function mount(): void
    {
        $this->filtrosAbertos = $this->temFiltroAvancado();
    }

    // ---------- Reset de paginação ----------
    // O nome do hook precisa ser updated + NomeExatoDaPropriedade.
    // Um typo aqui não gera erro: o método simplesmente nunca é chamado.

    public function updatedBusca(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroAtivo(): void
    {
        $this->resetPage();
    }

    public function updatedCadastradoDe(): void
    {
        $this->resetPage();
    }

    // ---------- Helpers de estado ----------

    public function temFiltroAvancado(): bool
    {
        return $this->filtroAtivo !== '' || $this->cadastradoDe !== '';
    }

    public function temQualquerFiltro(): bool
    {
        return $this->temFiltroAvancado() || $this->busca !== '';
    }

    public function limparFiltros(): void
    {
        $this->reset(['busca', 'filtroAtivo', 'cadastradoDe']);
        $this->resetPage();
    }

    // ---------- Ações ----------

    public function alternarStatus(int $pessoaId, PessoaService $pessoaService): void
    {
        $pessoa = Pessoa::findOrFail($pessoaId);
        $pessoa = $pessoaService->alternarStatus($pessoa);

        // Argumentos NOMEADOS: viram as chaves de $event.detail no Alpine.
        // Posicionais gerariam detail = [0 => 'success', 1 => '...'].
        $this->dispatch(
            'toast',
            tipo: 'success',
            mensagem: $pessoa->eh_ativo ? 'Pessoa ativada.' : 'Pessoa desativada.'
        );
    }

    public function excluir(int $pessoaId, PessoaService $pessoaService): void
    {
        $pessoa = Pessoa::findOrFail($pessoaId);
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
            'search'       => $this->busca,
            'ativo'        => $this->filtroAtivo,
            'created_from' => $this->cadastradoDe,
        ]);

        return view('livewire.pessoas.index', compact('pessoas'));
    }
}