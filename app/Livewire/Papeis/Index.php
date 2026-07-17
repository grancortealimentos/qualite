<?php

namespace App\Livewire\Papeis;

use App\Models\Papel;
use App\Repositories\PapelRepository;
use App\Services\PapelService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busca = '';

    public function updatingBusca(): void
    {
        $this->resetPage();
    }

    public function excluir(int $id, PapelService $papelService): void
    {
        $papel = Papel::find($id);
        if(!$papel) {
            $this->dispatch('toast', 'error', __('Papel não encontrado'));
            return;
        }

        try
        {
            $nome = $papel->name;
            $papelService->delete($papel);
            $this->dispatch('toast', 'success', __("Papel \"{$nome}\" excluído com sucesso."));
        }
        catch(ValidationException $e)
        {
            $this->dispatch('toast', 'error', $e->getMessage());
        }
    }

    public function temQualquerFiltro(): bool
    {
        return $this->busca !== '';
    }

    public function limparFiltros(): void
    {
        $this->reset('busca');
        $this->resetPage();
    }

    public function render(PapelRepository $papelRepository)
    {
        return view('livewire.papeis.index', [
            'papeis' => $papelRepository->paginar($this->busca ?: null)
        ])->layout('components.layouts.app');
    }
}
