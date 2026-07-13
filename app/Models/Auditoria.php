<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $table = 'auditorias';

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'before_data',
        'after_data',
        'ip_address',
        'user_agent',
        'correlation_id',
        'created_at',
    ];

    protected $casts = [
        'before_data' => 'array',
        'after_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope utilitario: historico de uma entidade especifica.
     * Uso: Auditoria::daEntidade('Propriedade', $propriedade->id)->get();
    */
    public function scopeDaEntidade($query, string $entityType, int|string $entityId)
    {
        return $query->where('entity_type', $entityType)
            ->where('entity_id', (string) $entityId)
            ->orderByDesc('created_at');
    }
}
