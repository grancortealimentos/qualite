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
            'usuario_force_password_change' => $this->boolean('usuario_force_password_change')
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
        ];
    }

    public function messages(): array
    {
        return [
            'usuario_email.unique' => 'Este e-mail já está em uso por outro usuário.'
        ];
    }
}