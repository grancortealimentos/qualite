<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filial extends Model
{
    use Auditavel, SoftDeletes;

    protected $table = 'filiais';

    protected $fillable = [
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
}
