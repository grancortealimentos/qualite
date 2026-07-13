<?php

namespace App\Http\Controllers;

use App\DTO\PessoaData;
use App\Http\Requests\PessoaRequest;
use App\Models\Pessoa;
use App\Services\PessoaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PessoaController extends Controller
{
    public function __construct(
        private readonly PessoaService $pessoaService
    ) {}

    public function create(): View
    {
        return view('dashboard.pessoas.create');
    }

    public function store(PessoaRequest $request): RedirectResponse
    {
        $this->pessoaService->create(
            PessoaData::fromArray($request->validated()),
            $request->file('url_foto_perfil')
        );
        return redirect()
            ->route('pessoas.index')
            ->with('status', 'Registro cadastrada com sucesso');
    }

    public function edit(Pessoa $pessoa): View
    {
        $pessoa = $pessoa->load('usuario');
        Log::debug('[CTRL][PESSOA][EDIT][PESSOA]', [$pessoa]);
        
        return view('dashboard.pessoas.edit', compact('pessoa'));
    }

    public function update(PessoaRequest $request, Pessoa $pessoa): RedirectResponse
    {
        $this->pessoaService->update(
            $pessoa, 
            PessoaData::fromArray($request->validated()),
            $request->file('url_foto_perfil')
        );

        return redirect()
            ->route('pessoas.index')
            ->with('status', 'Registro atualizado com sucesso.');
    }
}