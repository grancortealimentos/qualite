<?php

namespace App\Services;

use App\Models\ErroLog;
use App\Support\CorrelationId;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class LogErroService
{
    protected static array $jaRegistrados = [];

    protected array $camposSensiveisPayload = [
        'password',
        'password_confirmation',
        'previous_password',
        'token',
        'card_number',
        'cvv',
    ];

    protected array $headersSensiveis = [
        'authorization',
        'cookie',
        'x-xsrf-token',
        'x-csrf-token',
    ];

    public function __construct(
        protected CorrelationId $correlationId,
    ) {}

    public function registrarExcecao(
        Throwable $e,
        array $extra = [],
        ?string $jobName = null
    ): ?ErroLog
    {
        $chave = spl_object_id($e);
        if(isset(self::$jaRegistrados[$chave])) {
            return null;
        }

        self::$jaRegistrados[$chave] = true;

        $statusCode = $this->resolverStatusCode($e);
        $temRequisicao = app()->bound('request') && !app()->runningInConsole();

        return ErroLog::create([
            'user_id' => Auth::id(),
            'is_resolved' => false,
            'source' => $jobName ? 'queue' : (app()->runningInConsole() ? 'console' : 'http'),
            'level' => $this->resolverLevel($statusCode),
            'exception_class' => get_class($e),
            'message' => $e->getMessage() ?: class_basename($e),
            'file' => $e->getFile(),
            'line' => (string) $e->getLine(),
            'stack_trace' => Str::limit($e->getTraceAsString(), 10000, ''),
            'http_method' => $temRequisicao ? request()->method() : null,
            'url' => $temRequisicao ? request()->fullUrl() : null,
            'route_name' => $temRequisicao ? request()->route()?->getName() : null,
            'query_params' => $temRequisicao ? json_encode(request()->query()) : null,
            'request_payload' => $temRequisicao ? $this->redigir(request()->all(), $this->camposSensiveisPayload) : null,
            'request_headers' => $temRequisicao ? $this->redigirHeaders(request()->headers->all()) : null,
            'ip_address' => $temRequisicao ? request()->ip() : null,
            'user_agent' => $temRequisicao ? Str::limit((string) request()->userAgent(), 490, '') : null,
            'app_module' => $extra['app_module'] ?? null,
            'job_name' => $jobName,
            'correlation_id' => $this->correlationId->get(),
            'extra_data' => empty($extra) ? null : $extra,
        ]);
    }

    protected function resolverStatusCode(Throwable $e): int
    {
        return match(true) {
            $e instanceof ValidationException => $e->status,
            $e instanceof AuthenticationException => 401,
            $e instanceof AuthorizationException => 403,
            $e instanceof HttpExceptionInterface => $e->getStatusCode(),
            default => 500,
        };
    }

    protected function resolverLevel(int $statusCode): string
    {
        return match(true) {
            $statusCode >= 500 => 'error',
            $statusCode >= 400 => 'warning',
            default => 'info'
        };
    }

    protected function redigir(array $dados, array $camposSensiveis): array
    {
        foreach($dados as $chave => $valor) {
            if(is_array($valor)) {
                $dados[$chave] = $this->redigir($valor, $camposSensiveis);
                continue;
            }

            if(in_array(Str::lower((string) $chave), $camposSensiveis, true)) {
                $dados[$chave] = '***REDIGIDO***';
            }
        }

        return $dados;
    }

    protected function redigirHeaders(array $headers): array
    {
        foreach($headers as $nome => $valor) {
            if(in_array(Str::lower($nome), $this->headersSensiveis, true)) {
                $headers[$nome] = ['***REDIGIDO***'];
            }
        }

        return $headers;
    }
}