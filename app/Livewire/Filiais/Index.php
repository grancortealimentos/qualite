<?php

namespace App\Livewire\Filiais;

use App\Models\Filial;
use App\Services\FilialService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Filiais')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search', except: '')]
    public string $busca = '';

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
    public function alternarStatus(int $filialId, FilialService $filialService): void
    {
        if(!auth()->user()->can('filiais.status')) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Você não tem permissão para alterar status.');
            return;
        }

        $filial = Filial::findOrFail($filialId);
        $filial = $filialService->alterarStatus(filial: $filial);

        $this->dispatch(
            'toast',
            tipo: 'success',
            mensagem: $filial->eh_ativo ? 'Filial ativada.' : 'Filial desativada.'
        );
    }

    public function excluir(int $filialId, FilialService $filialService): void
    {
        if(!auth()->user()->can('filiais.excluir')) {
            $this->dispatch('toast', tipo: 'error', mensagem: 'Você não tem permissão para excluir.');
            return;
        }

        $filial = Filial::findOrFail($filialId);
        $filialService->delete(filial: $filial);

        $this->dispatch('toast', tipo: 'success', mensagem: 'Filial excluída com sucesso.');
    }

    public function render(FilialService $filialService)
    {
        $filiais = $filialService->listar([
            'search'       => $this->busca,
            'ativo'        => $this->filtroAtivo,
            'created_from' => $this->cadastradoDe,
        ]);

        return view('livewire.filiais.index', compact('filiais'));
    }
}