<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;


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
         /*! Force HTTPS in production environment
         * This is important when your application is behind a load balancer or proxy that terminates SSL.
         * It ensures that all generated URLs use the HTTPS scheme, which is crucial for security and proper functioning of the application.
         */
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        // Set default string length for database schema to avoid issues with older MySQL versions
        Schema::defaultStringLength(191);

    }
}
