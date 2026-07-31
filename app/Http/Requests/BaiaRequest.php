<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaiaRequest extends FormRequest
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
            'galpao_id' => ['required', 'integer', 'exists:galpoes,id'],
            'lote' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:255'],
        ];
    }
}