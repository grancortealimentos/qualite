<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, Auditavel;

    protected $table = 'users';

    protected $fillable = [
        'pessoa_id',
        'is_active', //ativo ou inativo
        'name',
        'email',
        'password',
        'force_password_change', //forço a troca de senha
        'previous_password', //gravo a senha anterior
        'password_reset_token', 
        'password_reset_expires_at', //data de quando precisa alterar a senha
        'email_verified_at',
        'password_changed_at', //data de quando a senha foi alterada
    ];

    protected $hidden = [
        'password',
        'previous_password',
        'password_reset_token',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'previous_password' => 'hashed',
        'is_active' => 'boolean',
        'password_reset_expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
    ];

    /**
        * Usado pelo middleware EnsurePasswordIsChanged (RN-005/RN-006/RN-007).
    */
    public function mustChangePassword(): bool
    {
        return (bool) $this->force_password_change;
    }

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function acessosPropriedades(): HasMany
    {
        // return $this->hasMany();
    }
}
