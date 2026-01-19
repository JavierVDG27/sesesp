<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

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
        // Solo ejecutamos la migración si estamos en Render (producción)
    if (config('app.env') === 'production') {
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
            // Si ya están creadas, no pasará nada
        }
    }
    }
}
