<?php

namespace App\Http\Controllers;

use App\DTO\PapelData;
use App\Http\Requests\PapelRequest;
use App\Models\Papel;
use App\Services\PapelService;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;


class PapelController extends Controller
{
    public function __construct(
        private readonly PapelService $papelService
    ) {}

    public function create(): View
    {
        return view('dashboard.permissoes.create', [
            'grupos' => $this->gruposOrdenados()
        ]);
    }

    public function edit(Papel $papel): View
    {
        $papel->load('permissions');
        return view('dashboard.permissoes.edit', [
            'papel' => $papel,
            'grupos' => $this->gruposOrdenados(),
            'permissoesMarcadas' => $papel->permissions->pluck('name')->all(),
        ]);
    }

    public function store(PapelRequest $request): RedirectResponse
    {
        try
        {
            $papel = $this->papelService->create(
                PapelData::fromArray($request->validated())
            );

            return redirect()
                ->route('papeis.index')
                ->with('toast', [
                    'tipo' => 'success',
                    'mensagem' => "Papel \"{$papel->name}\" criado com sucesso."
                ]);
        }
        catch(ValidationException $e)
        {
            return back()
                ->withInput()
                ->with('toast', [
                    'tipo' => 'error',
                    'mensagem' => $e->getMessage()
                ]);
        }
    }

    public function update(PapelRequest $request, Papel $papel): RedirectResponse
    {
        try
        {
            $this->papelService->update(
                $papel,
                PapelData::fromArray($request->validated())
            );

            return redirect()
                ->route('papeis.index')
                ->with('toast', [
                    'tipo' => 'success',
                    'mensagem' => "Papel \"{$papel->name}\" atualizado com sucesso."
                ]);
        }
        catch(ValidationException $e)
        {
            return back()
                ->withInput()
                ->with('toast', [
                    'tipo' => 'error',
                    'mensagem' => $e->getMessage()
                ]);
        }
    }

    public function destroy(Papel $papel): RedirectResponse
    {
        try
        {
            $nome = $papel->name;
            $this->papelService->delete($papel);

            return redirect()
                ->route('papeis.index')
                ->with('toast', [
                    'tipo' => 'success',
                    'mensagem' => "Papel \"{$nome}\" excluído com sucesso."
                ]); 
        }
        catch(ValidationException $e)
        {
            return back()
                ->with('toast', [
                    'tipo' => 'error',
                    'mensagem' => $e->getMessage()
                ]);
        }
    }

    /**
     * Devolve o catálogo de config/permissoes.php ordenado pelo código do
     * módulo, que é o que define a ordem de linhas na coluna.
    */
    private function gruposOrdenados(): array
    {
        $grupos = config('permissoes.grupos', []);
        uasort($grupos, fn ($a, $b) => strcmp($a['codigo'], $b['codigo']));

        return $grupos;
    }
}