<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErroLog extends Model
{
    public $timestamps = false;

    protected $table = 'erro_logs';

    protected $fillable = [
        'user_id',
        'resolved_by',
        'is_resolved',
        'source',
        'level', 
        'exception_class',
        'message',
        'file',
        'line',
        'stack_trace',
        'http_method',
        'url',
        'route_name',
        'query_params',
        'request_payload',
        'request_headers',
        'ip_address',
        'user_agent',
        'app_module',
        'job_name',
        'correlation_id',
        'extra_data',
        'resolution_note',
        'resolved_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'request_payload' => 'array',
        'request_headers' => 'array',
        'extra_data' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolvidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopeNaoResolvidos($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeNivel($query, string $level)
    {
        return $query->where('level', $level);
    }

    public function scopeDaMesmaRequisicao($query, string $correlationId)
    {
        return $query->where('correlation_id', $correlationId);
    }

    public function marcarComoResolvido(User $admin, ?string $nota = null): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_by' => $admin->id,
            'resolution_note' => $nota,
            'resolved_at' => now(),
        ]);
    }
}
