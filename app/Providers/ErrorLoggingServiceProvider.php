<?php

namespace App\Providers;

use App\Services\LogErroService;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ErrorLoggingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(JobExceptionOccurred::class, function (JobExceptionOccurred $event) {
            app(LogErroService::class)->registrarExcecao(
                $event->exception,
                [],
                $event->job->resolveName(),
            );
        });
    }
}