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
    // If the website is being accessed via an external live railway domain, force HTTPS
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    } elseif (str_contains(request()->getHttpHost(), 'railway.app')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
}
