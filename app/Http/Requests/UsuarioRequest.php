<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'usuario_force_password_change' => $this->boolean('usuario_force_password_change'),
            'usuario_papel_id' => $this->filled('usuario_papel_id')
                ? (int) $this->input('usuario_papel_id')
                : null,
        ]);
    }

    public function rules(): array
    {
        //usuario editado é sempre o vinculado à pessoa da rota.
        $usuarioId = $this->route('pessoa')?->usuario?->id;

        return [
            'usuario_name' => ['required', 'string', 'max:255'],
            'usuario_email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')
                    ->ignore($usuarioId)
                    ->whereNull('deleted_at'),
            ],
            'usuario_force_password_change' => ['boolean'],
            'usuario_password_expires_at' => ['nullable', 'date'],
            'usuario_papel_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', 'web'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'usuario_email.unique' => 'Este e-mail já está em uso por outro usuário.',
            'usuario_papel_id.required' => 'Selecione o papel do usuário.',
            'usuario_papel_id.exists'   => 'O papel selecionado não existe.',
        ];
    }
}