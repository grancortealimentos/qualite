<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }

    public function boot(): void
    {
        Password::defaults(function  () {
            return Password::min(8)
                ->mixedCase()
                ->symbols();
        });

        Gate::before(function ($user, $ability) {
            return $user->hasRole(config('permissoes.papel_administrador')) 
                ? true 
                : null;
        });
    }
}
