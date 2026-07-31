<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Baia extends Model
{
    use Auditavel, SoftDeletes;

    protected $table = 'baias';

    protected $fillable = [
        'eh_ativo',
        'galpao_id',
        'lote',
        'descricao',
    ];

    protected $casts = [
        'eh_ativo' => 'boolean',
    ];

    public function galpao(): BelongsTo
    {
        return $this->belongsTo(Galpao::class, 'galpao_id');
    }
}
