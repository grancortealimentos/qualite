<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Galpao extends Model
{
    use Auditavel, SoftDeletes;

    protected $table = 'galpoes';

    protected $fillable = [
        'propriedade_id',
        'tipo', //berçario, terminador 
        'nome',
    ];

    public function propriedade(): BelongsTo
    {
        return $this->belongsTo(Propriedade::class, 'propriedade_id');
    }
}
