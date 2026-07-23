<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filial extends Model
{
    use Auditavel, SoftDeletes;

    protected $table = 'filiais';

    protected $fillable = [
        'codigo',
        'eh_ativo',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'ie',
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'pais',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'eh_ativo' => 'boolean',
    ];

    protected function cnpjFormatado(): Attribute
    {
        return Attribute::make(
            function (): ?string {
                if(empty($this->cnpj)) {
                    return null;
                }

                if(strlen($this->cnpj) !== 14) {
                    return $this->cnpj;
                }

                return preg_replace(
                    '/^(\w{2})(\w{3})(\w{3})(\w{4})(\w{2})$/',
                    '$1.$2.$3/$4-$5',
                    $this->cnpj
                );
            }
        );
    }

    /**
     * Remonta a mascara do cep para exibição.
     * Uso na view: {{ $pessoa->cep_formatado}}
    */
    public function getCepFormatadoAttribute(): ?string
    {
        return $this->cep && strlen($this->cep) === 8
            ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $this->cep)
            : $this->cep;
    }
}
