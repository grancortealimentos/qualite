<?php

namespace App\Http\Requests;

use App\Repositories\PapelRepository;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PapelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza a entrada antes da validação rodar.
    */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nome' => trim((string) $this->input('nome')),
            'permissoes' => $this->input('permissoes', []),
        ]);
    }

    public function rules(): array
    {
        $papelId = $this->route('papel')?->id;

        return [
            'nome' => ['required', 'string', 'max:255', $this->regraNomeUnico($papelId)],
            'permissoes' => ['present', 'array'],
            'permissoes.*' => ['string', Rule::in($this->permissoesDoCatalogo())]
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome do papel.',
            'nome.unique' => 'Já existe um papel com este nome.',
            'permissoes.*.in' => 'Uma das permissoes enviadas não existe no sistema.'
        ];
    }

    /**
     * Regra de unicidade case-insensitive do nome do papel
    */
    private function regraNomeUnico(?int $papelId): Closure
    {
        return function(string $atributo, mixed $valor, Closure $falhar) use ($papelId) {
            if(app(PapelRepository::class)->existeComNome((string) $valor, $papelId)) {
                $falhar('Já existe um papel com este nome.');
            }
        };
    }

    /**
     * Achata o catálogo do config e uma lista simples de nomes técnicos,
     * para alimentar o Rule::in
    */
    private function permissoesDoCatalogo(): array
    {
        $nomes = [];
        foreach(config('permissoes.grupos', []) as $grupo) {
            $nomes = array_merge($nomes, array_keys($grupo['permissoes'] ?? []));
        }

        return $nomes;
    }
}