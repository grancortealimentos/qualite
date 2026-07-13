<?php

namespace App\Models\Concerns;

use App\Services\AuditoriaService;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait de aplicação SELETIVA (manual) da auditoria automática de eventos de model.
 * 
 * uso no model desejado:
 * class Propriedades extends Model
 * {
 *      use Auditavel;
 * 
 *      protected array $auditavelIgnorar = ['password', 'previous_password']
 * }
 * 
 * 
*/
trait Auditavel
{
    protected static function bootAuditavel(): void
    {
        static::created(function (Model $model) {
            $model->registrarAuditoria(
                'criado',
                null,
                $model->auditavelSnapshot($model->getAttributes())
            );
        });

        static::updated(function (Model $model) {
            $depois = $model->auditavelSnapshot($model->getChanges());

            //se nada relevante mudou (ex: só timestamps ignorados) -> não gera ruido na auditoria
            if(empty($depois)) {
                return;
            }

            $antes = $model->auditavelSnapshot($model->getOriginal());
            $antes = array_intersect_key($antes, $depois);

            $model->registrarAuditoria(
                'atualizado',
                $antes,
                $depois,
            );
        });

        static::deleted(function (Model $model) {
            $usandoSoftDelete = method_exists($model, 'isForceDeleting');
            $acao = ($usandoSoftDelete && !$model->isForceDeleting())
                ? 'excluido'
                : 'excluido_permanentemente';

            $model->registrarAuditoria(
                $acao,
                $model->auditavelSnapshot($model->getAttributes()),
                null
            );
        });

        if(method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                $model->registrarAuditoria(
                    'restaurado',
                    null,
                    $model->auditavelSnapshot($model->getAttributes())
                );
            });
        }
    }

    protected function registrarAuditoria(
        string $acao,
        ?array $antes,
        ?array $depois,
    ): void
    {
        app(AuditoriaService::class)->registrar(
            $acao,
            class_basename(static::class),
            (string) $this->getKey(),
            $antes,
            $depois
        );
    }

    /**
     * Remove campos sensíveis/irrelevantes do array antes de gravar na auditoria.
     * Nunca deve vazar hash de senha, tokens, etc.
    */
    protected function auditavelSnapshot(array $atributos): array
    {
        $ignorarPadrao = [
            'password',
            'previous_password',
            'password_reset_token',
            'remember_token',
            'updated_at'
        ];

        $ignorar = property_exists($this, 'auditavelIgnorar')
            ? array_unique(array_merge($ignorarPadrao, $this->auditavelIgnorar))
            : $ignorarPadrao;

        return collect($atributos)->except($ignorar)->toArray();
    }
}