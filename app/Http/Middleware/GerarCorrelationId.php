<?php

namespace App\Http\Middleware;

use App\Support\CorrelationId;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class GerarCorrelationId
{
    /**
     * Gera 1 ULID por requisição HTTP, compartilhado por toda as ações 
     * (auditoria, logs de erro) geradas durante a request.
     * 
     * Se a requisição já chegar com um header X-Correlation-Id 
     * (ex: vinda de outro serviço interno), esse valor é propagado em vez de 
     * gerar um novo - util para rastrear uma operação de ponta a ponta entre sistemas.
    */
    public function handle(Request $request, Closure $next): Response
    {
        $correlationId = app(CorrelationId::class);
        $idRecebido = $request->header('X-Correlation-Id');
        $correlationId->set($idRecebido ?: (string) Str::ulid());
        $response = $next($request);
        $response->headers->set('X-Correlation-Id', $correlationId->get());

        return $response;
    }
}
