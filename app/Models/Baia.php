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
        'galpao_id',
        'lote',
        'descricao',
    ];

    public function galpao(): BelongsTo
    {
        return $this->belongsTo(Galpao::class, 'galpao_id');
    }
}
