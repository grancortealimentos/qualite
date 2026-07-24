<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropriedadeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cnpj' => $this->apenasDigitos(valor: $this->input('cnpj')),
            'cep' => $this->apenasDigitos(valor: $this->input('cep')),
            'eh_ativo' => $this->boolean('eh_ativo'),
            'area_total' => $this->paraDecimal(valor: $this->input('area_total')),
            'area_consolidada' => $this->paraDecimal(valor: $this->input('area_consolidada')),
            'area_reservada_legal' => $this->paraDecimal(valor: $this->input('area_reservada_legal')),
            'area_app' => $this->paraDecimal(valor: $this->input('area_app')),
        ]);
    }

    public function rules(): array
    {
        $propriedadeId = $this->route('propriedade')?->id;

        return [
            'produtor_id' => ['required', 'integer', 'exists:pessoas,id'],
            'eh_ativo' => ['boolean'],
            'razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['nullable', 'string', 'max:255'],
            'cnpj' => [
                'required',
                'string',
                'size:14',
                'cnpj',
                Rule::unique('propriedades', 'cnpj')->ignore($propriedadeId)->whereNull('deleted_at'),
            ],
            'ie' => ['nullable', 'string', 'max:20'],
            'nrif' => ['nullable', 'string', 'max:50'],
            'car' => ['nullable', 'string', 'max:10'],
            'area_total' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'area_consolidada' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'area_reservada_legal' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'area_app' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'capacidade_armazenamento' => ['nullable', 'integer', 'min:0'],
            'cep' => ['nullable', 'string', 'size:8'],
            'logradouro' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:10'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
            'pais' => ['nullable', 'string', 'max:255'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'string', 'max:255'],
            'longitude' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'produtor_id.required' => 'Selecione o produtor responsável pela propriedade.',
            'produtor_id.exists' => 'O produtor selecionado não foi encontrado.',
            'razao_social.required' => 'Informe a razão social.',
            'cnpj.required' => 'Informe o CNPJ.',
            'cnpj.size' => 'O CNPJ deve conter 14 dígitos.',
            'cnpj.unique' => 'Já existe uma propriedade cadastrada com este CNPJ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'produtor_id' => 'produtor',
            'razao_social' => 'razão social',
            'nome_fantasia' => 'nome fantasia',
            'ie' => 'inscrição estadual',
            'area_total' => 'área total',
            'area_consolidada' => 'área consolidada',
            'area_reservada_legal' => 'área de reserva legal',
            'area_app' => 'área de APP',
            'capacidade_armazenamento' => 'capacidade de armazenamento',
        ];
    }

    private function apenasDigitos(?string $valor): ?string
    {
        if(blank($valor)) {
            return null;
        }

        $digitos = preg_replace('/\D/', '', $valor);
        return filled($digitos) ? $digitos : null;
    }

    private function paraDecimal(mixed $valor): ?float
    {
        if(blank($valor)) {
            return null;
        }

        if(is_numeric($valor)) {
            return (float) $valor;
        }

        $normalizado = str_replace(['.', ','], ['', '.'], (string) $valor);

        return is_numeric($normalizado) ? (float) $normalizado : null;
    }
}