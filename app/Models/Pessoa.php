<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Pessoa extends Model
{
    use SoftDeletes, Auditavel;

    protected $table = 'pessoas';

    protected $fillable = [
        'eh_ativo',
        'url_foto_perfil',
        'tipo_cadastro', //enum: veterinario, funcionario, 
        'nome_completo',
        'tipo_documento', //enum: cpf ou cnpj
        'documento',
        'doc_profissional', // crmv, oab, crm
        'telefone',
        'email', 
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'pais', //fixo Brasil
    ];

    protected $casts = [
        'eh_ativo' => 'boolean'
    ];

    public function usuario(): HasOne
    {
        return $this->hasOne(User::class, 'pessoa_id');
    }

    //a pessoa tem acesso ao sistema?
    public function temUsuario(): bool
    {
        return $this->usuario()->exists();
    }

    // ---------- Accessors de apresentação ----------

    /**
     * URL publica da foto.
     * Uso na view {{ $pessoa->foto_url }}
    */
    public function getFotoUrlAttribute(): ?string
    {
        if(!$this->url_foto_perfil) {
            return null;
        }

        return Storage::disk('public')->url(ltrim($this->url_foto_perfil,'/'));
    }

    /**
     * Remonta a mascara do documento para exibição.
     * Uso na view: {{ $pessoa->documento_formatado }}
    */
    public function getDocumentoFormatadoAttribute(): ?string
    {
        $doc = $this->documento;
        if(!$doc) {
            return null;
        }

        return match (strlen($doc)) {
            11 => preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc),
            14 => preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc),
            default => $doc,
        };
    }

    /**
     * Remonta a mascara do telefone para exibição.
     * Uso na view: {{ $pessoa->telefone_formatado }}
    */
    public function getTelefoneFormatadoAttribute(): ?string
    {
        $tel = $this->telefone;
        if(!$tel) {
            return null;
        }

        return match (strlen($tel)) {
            10 => preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $tel),
            11 => preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $tel),
            default => $tel,
        };
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
