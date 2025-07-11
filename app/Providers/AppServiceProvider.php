<?php

namespace App\Providers;

use App\Services\ApiResponseService;
use App\Services\Interfaces\ApiResponseServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the ApiResponseService as a singleton
        $this->app->singleton(ApiResponseServiceInterface::class, function ($app) {
            return new ApiResponseService();
        });

        // Also bind the concrete implementation
        $this->app->singleton(ApiResponseService::class, function ($app) {
            return $app->make(ApiResponseServiceInterface::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
