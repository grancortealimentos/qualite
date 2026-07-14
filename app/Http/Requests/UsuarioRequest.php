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

    public function rules(): array
    {
        $usuarioId = $this->route('pessoa')?->usuario?->id;

        return [
            'usuario_name' => ['required', 'string', 'max:255'],
            'usuario_email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')
                    ->ignore($usuarioId)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'usuario_email.unique' => 'Este e-mail já está em uso por outro usuário.'
        ];
    }
}