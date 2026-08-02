<?php

namespace App\Providers;

use App\Services\Intelligence\Providers\AnthropicProvider;
use App\Services\Intelligence\Providers\LlmProviderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LlmProviderInterface::class, AnthropicProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
