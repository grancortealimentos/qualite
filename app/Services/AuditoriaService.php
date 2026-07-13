<?php

namespace App\Services;

use App\Models\Auditoria;
use App\Support\CorrelationId;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class AuditoriaService
{
    public function __construct(
        protected CorrelationId $correlationId
    ) {}

    public function registrar(
        string $acao,
        string $entidadeTipo,
        string $entidadeId,
        ?array $antes = null,
        ?array $depois = null,
        ?string $descricao = null,
        ?int $userId = null,
    ): Auditoria
    {
        return Auditoria::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $acao,
            'entity_type' => $entidadeTipo,
            'entity_id' => $entidadeId,
            'description' => $descricao,
            'before_data' => $antes,
            'after_data' => $depois,
            'ip_address' => Request::ip(),
            'user_agent' => Str::limit((string) Request::userAgent(), 490, ''),
            'correlation_id' => $this->correlationId->get()
        ]);
    }
}