<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilialRequest extends FormRequest
{
    /**
     * A autorização fica nas rotas (middleware can:), seguindo o padrão do projeto.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Remove máscaras antes da validação, para que as regras (dígitos, tamanho)
     * e a persistência trabalhem sempre com o valor cru — mesmo esquema do PessoaRequest.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Só dígitos em CNPJ e CEP.
            'cnpj' => $this->cnpj ? preg_replace('/\D/', '', $this->cnpj) : null,
            'cep'  => $this->cep ? preg_replace('/\D/', '', $this->cep) : null,
            // Checkbox/toggle do formulário chega como on/1/true → normaliza para boolean.
            'eh_ativo' => $this->boolean('eh_ativo'),
        ]);
    }

    /**
     * Regras de validação da filial.
     */
    public function rules(): array
    {
        return [
            'eh_ativo'      => ['boolean'],
            'razao_social'  => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['nullable', 'string', 'max:255'],

            // CNPJ opcional; quando informado, precisa ter 14 dígitos e passar no dígito verificador.
            'cnpj' => ['nullable', 'string', 'size:14', $this->regraCnpjValido()],

            'ie'  => ['nullable', 'string', 'max:20'],

            'cep'        => ['nullable', 'string', 'size:8'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero'     => ['nullable', 'string', 'max:20'],
            'bairro'     => ['nullable', 'string', 'max:255'],
            'cidade'     => ['nullable', 'string', 'max:255'],
            'estado'     => ['nullable', 'string', 'max:255'],
            'pais'       => ['nullable', 'string', 'max:255'],

            'latitude'   => ['nullable', 'string', 'max:255'],
            'longitude'  => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Mensagens amigáveis para os erros mais comuns.
     */
    public function messages(): array
    {
        return [
            'razao_social.required' => 'A razão social é obrigatória.',
            'cnpj.size'             => 'O CNPJ deve conter 14 dígitos.',
            'cep.size'              => 'O CEP deve conter 8 dígitos.',
        ];
    }

    /**
     * Nomes dos campos usados nas mensagens de erro.
     */
    public function attributes(): array
    {
        return [
            'razao_social'  => 'razão social',
            'nome_fantasia' => 'nome fantasia',
            'cnpj'          => 'CNPJ',
            'ie'            => 'inscrição estadual',
        ];
    }

    /**
     * Regra de closure que valida o dígito verificador do CNPJ.
     * Recebe o valor já sem máscara (graças ao prepareForValidation).
     */
    private function regraCnpjValido(): \Closure
    {
        return function (string $atributo, mixed $valor, \Closure $fail): void {
            if (! $this->cnpjEhValido(cnpj: $valor)) {
                $fail('O CNPJ informado é inválido.');
            }
        };
    }

    /**
     * Valida o CNPJ pelos dígitos verificadores (algoritmo padrão da Receita).
     */
    private function cnpjEhValido(string $cnpj): bool
    {
        // Rejeita tamanho errado ou sequências repetidas (ex.: 00000000000000).
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        // Cálculo dos dois dígitos verificadores.
        for ($t = 12; $t < 14; $t++) {
            $soma = 0;
            $peso = $t - 7;

            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cnpj[$i] * $peso;
                $peso = ($peso < 3) ? 9 : $peso - 1;
            }

            $digito = ((10 * $soma) % 11) % 10;

            if ((int) $cnpj[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }
}