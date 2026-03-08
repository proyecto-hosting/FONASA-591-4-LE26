<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- 1. Agrega esta línea

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 2. Le decimos a Laravel que si está en producción, SIEMPRE use HTTPS
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
