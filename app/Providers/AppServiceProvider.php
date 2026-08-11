<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
    public function register(): void
{
    $this->app->bind(
        \App\Services\Analyse\AnalyseIAService::class,
        \App\Services\Analyse\GeminiAnalyseIAService::class
    );
}
}
