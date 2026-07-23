<?php

namespace App\Http\Controllers;

use App\DTO\FilialData;
use App\Http\Requests\FilialRequest;
use App\Models\Filial;
use App\Services\FilialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class FilialController extends Controller
{
    public function __construct(
        private readonly FilialService $filialService
    ) {}

    public function create(): View
    {
        return view('dashboard.filiais.create');
    }

    public function edit(Filial $filial): View
    {
        return view('dashboard.filiais.edit', [
            'filial' => $filial,
        ]);
    }

    public function store(FilialRequest $request): RedirectResponse
    {
        try 
        {
            $this->filialService->create(
                data: FilialData::paraCriacao($request->validated())
            );

            return redirect()
                ->route('filiais.index')
                ->with('status', 'Registro cadastrado com sucesso.');
        } 
        catch (ValidationException $e) 
        {
            throw $e;
        } 
        catch (Throwable $e) 
        {
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Não foi possível cadastrar a filial.');
        }
    }

    public function update(FilialRequest $request, Filial $filial): RedirectResponse
    {
        try 
        {
            $this->filialService->update(
                filial: $filial,
                data: FilialData::paraEdicao($request->validated())
            );

            return redirect()
                ->route('filiais.index')
                ->with('status', 'Registro atualizado com sucesso.');
        } 
        catch (ValidationException $e) 
        {
            throw $e;
        } 
        catch (Throwable $e) 
        {
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Não foi possível atualizar a filial.');
        }
    }

    public function destroy(Filial $filial): RedirectResponse
    {
        try 
        {
            $this->filialService->delete(filial: $filial);

            return redirect()
                ->route('filiais.index')
                ->with('status', 'Filial removida com sucesso.');
        } 
        catch (Throwable $e) 
        {
            report($e);
            return back()
                ->with('error', 'Não foi possível remover a filial.');
        }
    }

    public function alterarStatus(Filial $filial): RedirectResponse
    {
        try 
        {
            $this->filialService->alterarStatus(filial: $filial);

            return back()
                ->with('status', 'Status da filial atualizado.');
        } 
        catch (Throwable $e) 
        {
            report($e);
            return back()
                ->with('error', 'Não foi possível alterar o status da filial.');
        }
    }
}