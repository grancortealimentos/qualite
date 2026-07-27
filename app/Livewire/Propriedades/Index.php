<?php

namespace App\Livewire\Propriedades;

use App\Models\Propriedade;
use App\Repositories\PropriedadeRepository;
use App\Services\PropriedadeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Throwable;

#[Layout('components.layouts.app')]
#[Title('Propriedades')]
class Index extends Component
{
    use WithPagination, AuthorizesRequests;

    // Filtros sincronizados na URL (permitem compartilhar/recarregar mantendo o estado).
    #[Url()]
    public string $busca = '';

    #[Url()]
    public string $filtroAtivo = '';

    #[Url()]
    public string $cadastradoDe = '';

    // Controle apenas de UI: não vai para a URL.
    public bool $filtrosAbertos = false;

    /** Volta para a página 1 sempre que a busca muda. */
    public function updatedBusca(): void
    {
        $this->resetPage();
    }

    /** Volta para a página 1 sempre que o filtro de status muda. */
    public function updatedFiltroAtivo(): void
    {
        $this->resetPage();
    }

    /** Volta para a página 1 sempre que a data inicial muda. */
    public function updatedCadastradoDe(): void
    {
        $this->resetPage();
    }

    /** Indica se há algum filtro avançado (status ou data) aplicado. */
    public function temFiltroAvancado(): bool
    {
        return $this->filtroAtivo !== '' || $this->cadastradoDe !== '';
    }

    /** Indica se há qualquer filtro aplicado, incluindo a busca. */
    public function temQualquerFiltro(): bool
    {
        return $this->busca !== '' || $this->temFiltroAvancado();
    }

    /** Zera todos os filtros e volta para a primeira página. */
    public function limparFiltros(): void
    {
        $this->reset(['busca', 'filtroAtivo', 'cadastradoDe']);
        $this->resetPage();
    }

    /**
     * Ativa/inativa a propriedade via Service.
     * Verifica a permissão aqui porque a ação não passa por rota/middleware.
     */
    public function alternarStatus(int $id): void
    {
        $this->authorize('propriedades.alterar_status');

        try 
        {
            $propriedade = Propriedade::findOrFail($id);
            $novoStatus = ! $propriedade->eh_ativo;

            app(PropriedadeService::class)->alterarStatus(
                propriedade: $propriedade,
                ehAtivo: $novoStatus
            );

            $this->dispatch(
                'toast',
                tipo: 'success',
                mensagem: $novoStatus ? 'Propriedade ativada com sucesso.' : 'Propriedade inativada com sucesso.'
            );
        } 
        catch (Throwable $e) 
        {
            report($e);
            $this->dispatch('toast', tipo: 'error', mensagem: 'Não foi possível alterar o status da propriedade.');
        }
    }

    /**
     * Exclui logicamente a propriedade via Service.
     * Verifica a permissão aqui porque a ação não passa por rota/middleware.
     */
    public function excluir(int $id): void
    {
        $this->authorize('propriedades.excluir');

        try 
        {
            $propriedade = Propriedade::findOrFail($id);

            app(PropriedadeService::class)->delete(propriedade: $propriedade);

            $this->dispatch('toast', tipo: 'success', mensagem: 'Propriedade excluída com sucesso.');
        } 
        catch (Throwable $e) 
        {
            report($e);
            $this->dispatch('toast', tipo: 'error', mensagem: 'Não foi possível excluir a propriedade.');
        }
    }

    /**
     * Renderiza a listagem. O Repository é injetado por método (resolvido pelo container).
     */
    public function render(PropriedadeRepository $repository)
    {
        $propriedades = $repository->paginar(
            filtros: [
                'busca' => $this->busca,
                'status' => $this->filtroAtivo,
                'cadastrado_de' => $this->cadastradoDe,
            ],
            porPagina: 15
        );

        return view('livewire.propriedades.index', [
            'propriedades' => $propriedades,
        ]);
    }
}
