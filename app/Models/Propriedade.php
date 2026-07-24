<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propriedade extends Model
{
    use Auditavel, SoftDeletes;

    protected $table = 'propriedades';

    protected $fillable = [
        'produtor_id',
        'eh_ativo',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'ie',
        'nrif',
        'car',
        'area_total',
        'area_consolidada',
        'area_reservada_legal',
        'area_app',
        'capacidade_armazenamento',
        'cep', 
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'pais', 
        'complemento',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'eh_ativo' => 'boolean',
        'area_total' => 'decimal:2',
        'area_consolidada' => 'decimal:2',
        'area_reservada_legal' => 'decimal:2',
        'area_app' => 'decimal:2',
        'capacidade_armazenamento' => 'integer',
    ];

    public function produtor(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'produtor_id');
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------
    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('eh_ativo', true);
    }

    public function scopeBusca(Builder $query, ?string $termo): Builder
    {
        if(blank($termo)) {
            return $query;
        }

        $termoLimpo = preg_replace('/\D/', '', $termo);

        return $query->where(function (Builder $q) use ($termo, $termoLimpo) {
            $q->where('razao_social', 'ilike', "%{$termo}%")
                ->orWhere('nome_fantasia', 'ilike', "%{$termo}%");

            if(filled($termoLimpo)) {
                $q->orWhere('cnpj', 'ilike', "{$termoLimpo}");
            }
        });
    }

    // ------------------------------------------------------------------
    // Accessors (exibição — dados sempre gravados sem máscara)
    // ------------------------------------------------------------------
    public function getNomeExibicaoAttribute(): string
    {
        return $this->nome_fantasia ?: $this->razao_social;
    }

    public function getCnpjFormatadoAttribute(): ?string
    {
        if(blank($this->cnpj)) {
            return null;
        }

        return preg_replace(
            pattern: '/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/',
            replacement: '$1.$2.$3/$4-$5',
            subject: $this->cnpj
        );
    }

    public function getCepFormatadoAttribute(): ?string
    {
        if(blank($this->cep)) {
            return null;
        }

        return preg_replace('/^(\d{5})(\d{3})$/', '$1-$2', $this->cep); 
    }

    public function getEnderecoCompletoAttribute(): ?string
    {
        $partes = array_filter([
            $this->logradouro,
            $this->numero,
            $this->complemento,
            $this->bairro,
            $this->cidade && $this->estado ? "{$this->cidade}/{$this->estado}" : $this->cidade,
            $this->cep_formatado,
        ]);

        return filled($partes) ? implode(', ', $partes) : null;
    }
}
