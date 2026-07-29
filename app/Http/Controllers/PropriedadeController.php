<?php

namespace App\Http\Controllers;

use App\DTO\PropriedadeData;
use App\Http\Requests\PropriedadeRequest;
use App\Models\Pessoa;
use App\Models\Propriedade;
use App\Services\PropriedadeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PropriedadeController extends Controller
{
    public function __construct(
        private readonly PropriedadeService $propriedadeService
    ) {}

    public function create(): View
    {
        return view('dashboard.propriedades.create', [
            'produtores' => $this->listarProdutores()
        ]);
    }

    public function edit(Propriedade $propriedade): View
    {
        return view('dashboard.propriedades.edit', [
            'propriedade' => $propriedade,
            'produtores' => $this->listarProdutores()
        ]);
    }

    public function store(PropriedadeRequest $request): RedirectResponse
    {
        try
        {
            $propridade = $this->propriedadeService->create(
                PropriedadeData::paraCriacao($request->validated())
            );

            return redirect()
                ->route('propriedades.index', $propridade)
                ->with('status', 'Propriedade cadastrado com sucesso');
        }
        catch(ValidationException $e)
        {
            throw $e;
        }
        catch(Throwable $e)
        {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Não foi possível cadastrar a propriedade.');

        }
    }

    public function update(PropriedadeRequest $request, Propriedade $propriedade): RedirectResponse
    {
        try
        {
            $this->propriedadeService->update(
                $propriedade,
                PropriedadeData::paraEdicao($request->validated())
            );

            return redirect()
                ->route('propriedades.index', $propriedade)
                ->with('status', 'Propriedade atualizada com sucesso.');
        }
        catch(ValidationException $e)
        {
            throw $e;
        }
        catch(Throwable $e)
        {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Não foi possível atualizar a propriedade');
        }
    }

    public function destroy(Propriedade $propridade): RedirectResponse
    {
        try
        {
            $this->propriedadeService->delete($propridade);
            return redirect()
                ->route('propriedades.index')
                ->with('status', 'Propriedade exluída com sucesso.');
        }
        catch(ValidationException $e)
        {
            throw $e;
        }
        catch(Throwable $e)
        {
            report($e);
            return back()
                ->with('error', 'Não foi possível excluir a propriedade');
        }
    }

    public function alterarStatus(Propriedade $propridade): RedirectResponse
    {
        try
        {
            $novoStatus = !$propridade->eh_ativo;
            $this->propriedadeService->alterarStatus(
                $propridade,
                $novoStatus
            );

            return back()
                ->with(
                    'status',
                    $novoStatus ? 'Propriedade ativada com sucesso.' : 'Propriedade inativada com sucesso.'
                );
        }
        catch(ValidationException $e)
        {
            throw $e;
        }
        catch(Throwable $e)
        {
            report($e);
            return back()
                ->with('error', 'Não foi possível alterar o status da propriedade.');
        }
    }

    private function listarProdutores()
    {
        return Pessoa::query()
            ->where('eh_ativo', true)
            ->where('tipo_cadastro', 'Produtor')
            ->orderBy('nome_completo')
            ->get(['id', 'nome_completo', 'documento']);
    }
}