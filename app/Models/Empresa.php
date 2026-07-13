<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empresa extends Model
{
    use SoftDeletes, Auditavel;

    protected $table = 'empresas';

    protected $fillable = [
        'eh_ativo',
        'razao_social',
        'nome_fantasia',
        'tipo_documento', //enum: cpf ou cnpj
        'documento',
        'ie',
        'email',
        'telefone',
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'pais', //fixo Brasil
    ];

    protected $casts = [
        'eh_ativo' => 'boolean',
    ];

    public function propriedades(): HasMany
    {
        
    }
}
