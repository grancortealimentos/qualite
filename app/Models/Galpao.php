<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Galpao extends Model
{
    use Auditavel, SoftDeletes;

    protected $table = 'galpoes';

    protected $fillable = [
        'eh_ativo',
        'propriedade_id',
        'tipo', //berçario, terminador 
        'nome',
    ];

    protected $casts = [
        'eh_ativo' => 'boolean',
    ];

    public function propriedade(): BelongsTo
    {
        return $this->belongsTo(Propriedade::class, 'propriedade_id');
    }

    public function baias(): HasMany
    {
        return $this->hasMany(Baia::class, 'galpao_id');
    }

    public function scopeAtivos($query)
    {
        return $query->where('eh_ativo', true);
    }

    public function scopeBusca($query, string $termo)
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('nome', 'ilike', "%{$termo}%")
                ->orWhere('tipo', 'ilike', "%{$termo}%");
        });
    }
}
