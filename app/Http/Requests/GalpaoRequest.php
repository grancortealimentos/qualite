<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GalpaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'eh_ativo' => $this->boolean('eh_ativo'),
        ]);
    }

    public function rules(): array
    {
        return [
            'eh_ativo' => ['boolean'],
            'propriedade_id' => ['required', 'integer', 'exists:propriedades,id'],
            'tipo' => ['required', 'string', 'max:255'],
            'nome' => ['required', 'string', 'max:255'],
        ];
    }
}