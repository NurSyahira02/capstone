<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Bulletproof check: If the URL contains 'railway.app', force HTTPS
        if (str_contains(request()->url(), 'railway.app')) {
            URL::forceScheme('https');
        }
    }
}