<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PessoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Roda ANTES das rules
     * Remove as mascaras do frontend e normaliza o checkbox
    */
    protected function prepareForValidation(): void
    {
        $this->merge([
            //checkbox desmarcado não é enviado pelo browser.
            'eh_ativo' => $this->boolean('eh_ativo'),
            'criar_usuario' => $this->boolean('criar_usuario'),
            'usuario_force_password_change' => $this->boolean('usuario_force_password_change'),

            // remove pontos, traços, barras , parenteses e espaços
            'documento' => $this->limpar($this->input('documento')),
            'cep' => $this->limpar($this->input('cep')),
            'telefone' => $this->limpar($this->input('telefone')),

            // normaliza para o formato guardado no banco
            'tipo_documento' => $this->input('tipo_documento')
                ? strtoupper($this->input('tipo_documento'))
                : null,

            'estado' => $this->input('estado')
                ? strtoupper($this->input('estado'))
                : null,

            'usuario_papel_id' => [
                'exclude_unless:criar_usuario,true',
                'required',
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', 'web'),
            ],
        ]);
    }

    private function limpar(?string $valor): ?string
    {
        if($valor === null || $valor === '') {
            return null;
        }

        return preg_replace('/\D/', '', $valor) ?: null;
    }

    public function rules(): array
    {
        $pessoaId = $this->route('pessoa')?->id;

        return [
            'tipo_cadastro' => ['required', 'string', 'max:50'],
            'nome_completo' => ['required', 'string', 'min:3', 'max:255'],
            'eh_ativo' => ['required', 'boolean'],
            'tipo_documento' => ['nullable', Rule::in('CPF', 'CNPJ')],
            'documento' => ['nullable', 'string', 'max:14', Rule::unique('pessoas', 'documento')
                ->ignore($pessoaId)
                ->whereNull('deleted_at'),
            ],
            'doc_profissional' => ['nullable', 'string', 'max:20'],
            'telefone' => ['nullable', 'string', 'min:10', 'max:11'],
            'email' => ['nullable', 'email', 'max:255'],
            'url_foto_perfil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'cep' => ['nullable', 'string', 'size:8'],
            'logradouro' => ['nullable', 'string'],
            'numero' => ['nullable', 'string'],
            'bairro' => ['nullable', 'string'],
            'cidade' => ['nullable', 'string'],
            'estado' => ['nullable', 'string'],
            'pais' => ['nullable', 'string'],

            // --- Criação opcional de usuário ---
            // 'boolean' é OBRIGATÓRIO aqui: é ele que faz o exclude_unless
            // abaixo comparar o booleano real com 'true' corretamente.
            'criar_usuario' => ['boolean'],

            'usuario_name' => [
                'exclude_unless:criar_usuario,true',
                'nullable', 'string', 'max:255',
            ],

            'usuario_email' => [
                'exclude_unless:criar_usuario,true',
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'usuario_password' => [
                'exclude_unless:criar_usuario,true',
                'required', 'string',
                Password::defaults(),
                'confirmed', // espera o campo usuario_password_confirmation
            ],

            'usuario_force_password_change' => [
                'exclude_unless:criar_usuario,true',
                'boolean',
            ],

            'usuario_password_expires_at' => [
                'exclude_unless:criar_usuario,true',
                'nullable', 'date', 'after:now',
            ],

            'usuario_papel_id' => [
                'nullable',
                'required_if:criar_usuario,1',
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', 'web')
            ],
        ];
    }

    /**
     * Valida digito verificador de CPF/CNPJ 
    */
    public function after(): array
    {
        return [
            function ($validator) {
                $doc = $this->input('documento');
                $tipo = $this->input('tipo_documento');
                if(!$doc || !$tipo) {
                    return;
                }

                if($tipo === 'CPF' && !$this->cpfValido($doc)) {
                    $validator->errors()->add('documento', 'O CPF informado é inválido.');
                }

                if($tipo === 'CNPJ' && !$this->cnpjValido($doc)) {
                    $validator->errors()->add('documento', 'O CNPJ informado é inválido');
                }
            }
        ];
    }

    private function cpfValido(string $cpf): bool
    {
        if(strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for($t = 9; $t < 11; $t++) {
            $soma = 0;
            for($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * (($t + 1) - $i);
            }

            $digito = ((10 * $soma) % 11) % 10;
            if((int) $cpf[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }

    private function cnpjValido(string $cnpj): bool
    {
        if(strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $calcular = function (int $tamanho) use ($cnpj): int {
            $pesos = $tamanho === 12
                ? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
                : [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

            $soma = 0;
            for($i = 0; $i < $tamanho; $i++) {
                $soma += (int) $cnpj[$i] * $pesos[$i];
            }

            $resto = $soma % 11;

            return $resto < 2 ? 0 : 11 - $resto;
        };

        return $calcular(12) === (int) $cnpj[12]
            && $calcular(13) === (int) $cnpj[13];
    }

    public function attributes(): array
    {
        return [
            'tipo_cadastro' => 'tipo de cadastro',
            'nome_completo' => 'nome completo',
            'eh_ativo' => 'status',
            'tipo_documento' => 'tipo de documento',
            'doc_profissional' => 'documento profissional',
            'url_foto_perfil' => 'foto de perfil',
            'usuario_name' => 'nome do usuário',
            'usuario_email' => 'e-mail do usuário',
            'usuario_password' => 'senha do usuário',
            'usuario_force_password_change' => 'forçar troca de senha',
            'usuario_password_expires_at' => 'data da próxima troca de senha',
            'usuario_papel_id' => 'papel do usuário',
        ];
    }

    public function messages(): array
    {
        return [
            'documento.unique' => 'Este documento já está cadastrado.',
            'cep.size' => 'O CEP deve conter 8 digitos.',
            'estado.size' => 'O estado deve conter 2 letras (UF).',
            'usuario_email.unique' => 'Este e-mail já está em uso por outro usuário.',
            'usuario_email.required' => 'O e-mail é obrigatório para criar o acesso.',
            'usuario_password.required' => 'A senha é obrigatória para criar o acesso.',
            'usuario_password.confirmed' => 'A confirmação da senha não confere.',
            'usuario_password_expires_at.after' => 'A data da próxima troca deve ser futura.',
            'usuario_papel_id.required_if' => 'Selecione o papel do usuário.',
            'usuario_papel_id.exists'      => 'O papel selecionado não existe.',
        ];
    }
}