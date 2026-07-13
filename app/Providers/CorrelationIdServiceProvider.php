<?php

namespace App\Providers;

use App\Support\CorrelationId;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class CorrelationIdServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CorrelationId::class);
    }

    public function boot(): void
    {
        Queue::before((function(JobProcessing $event) {
            app(CorrelationId::class)->set((string) Str::ulid());
        }));
    }
}