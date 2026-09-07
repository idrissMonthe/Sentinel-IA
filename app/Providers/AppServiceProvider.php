<?php

namespace App\Providers;

use App\Services\Analyse\AnalyseIAService;
use App\Services\Analyse\OpenAIAnalyseIAService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
    }

    public function register(): void
    {
        $this->app->bind(AnalyseIAService::class, OpenAIAnalyseIAService::class);
    }
}
