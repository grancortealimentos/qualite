<?php

namespace App\Http\Controllers;

use App\DTO\PessoaData;
use App\Http\Requests\PessoaRequest;
use App\Http\Requests\UsuarioRequest;
use App\Models\Pessoa;
use App\Services\PessoaService;
use App\Services\UsuarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PessoaController extends Controller
{
    public function __construct(
        private readonly PessoaService $pessoaService,
        private readonly UsuarioService $usuarioService
    ) {}

    public function create(): View
    {
        return view('dashboard.pessoas.create');
    }

    public function store(PessoaRequest $request): RedirectResponse
    {
        try
        {
            $this->pessoaService->create(
                PessoaData::fromArray($request->validated()),
                $request->file('url_foto_perfil'),
                $this->extrairDadosUsuario($request->validated())
            );

            return redirect()
                ->route('pessoas.index')
                ->with('status', 'Registro cadastrada com sucesso');
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
                ->with('error', 'Não foi possível cadastrar o registro. Tente novamente');
        }
    }

    public function edit(Pessoa $pessoa): View
    {
        $pessoa = $pessoa->load('usuario');
        return view('dashboard.pessoas.edit', compact('pessoa'));
    }

    public function update(PessoaRequest $request, Pessoa $pessoa): RedirectResponse
    {
        try
        {
            $this->pessoaService->update(
                $pessoa, 
                PessoaData::fromArray($request->validated()),
                $request->file('url_foto_perfil'),
                $this->extrairDadosUsuario($request->validated())
            );
    
            return redirect()
                ->route('pessoas.index')
                ->with('status', 'Registro atualizado com sucesso.');
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
                ->with('error', 'Não foi possível atualizar o registro. Tente novamente.');
        }
    }

    public function revogarUsuario(Pessoa $pessoa): RedirectResponse
    {
        $usuario = $pessoa->usuario;
        if($usuario === null) {
            return back()
                ->with('error', 'Esta pessoa não possui usuário para revogar.');
        }

        try
        {
            $this->usuarioService->revogar($usuario);
            return back()
                ->with('status', 'Acesso do usuário revogado com sucesso.');
        }
        catch(Throwable $e)
        {
            report($e);
            return back()
                ->with('error', 'Não foi possível revogar acesso.');
        }
    }

    public function reativarUsuario(Pessoa $pessoa): RedirectResponse
    {
        $usuario = $pessoa->usuario;
        if($usuario === null) {
            return back()
                ->with('error', 'Esta pessoa não possui usuário para revogar.');
        }

        try
        {
            $this->usuarioService->reativar($usuario);
            return back()
                ->with('status', 'Acesso do usuário reativado com sucesso.');
        }
        catch(Throwable $e)
        {
            report($e);
            return back()
                ->with('error', 'Não foi possível reativar acesso.');
        }
    }

    private function extrairDadosUsuario(array $validated): ?array
    {
        if(empty($validated['criar_usuario'])) {
            return null;
        }

        return [
            'usuario_name'                  => $validated['usuario_name'] ?? null,
            'usuario_email'                 => $validated['usuario_email'],
            'usuario_password'              => $validated['usuario_password'],
            'usuario_force_password_change' => $validated['usuario_force_password_change'] ?? true,
            'usuario_password_expires_at'   => $validated['usuario_password_expires_at'] ?? null,
        ];
    }

    public function atualizarUsuario(UsuarioRequest $request, Pessoa $pessoa): RedirectResponse
    {
        $usuario = $pessoa->usuario;
        if($usuario === null) {
            return back()
                ->with('error', 'Esta pessoa não possui usuário para editar.');
        }

        try
        {
            $this->usuarioService->update($usuario, $request->validated());

            return back()
                ->with('status', 'Dados de acesso atualizados com sucesso.');
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
                ->with('error', 'Não foi possível atualizar os dados de acesso.');
        }
    }
}