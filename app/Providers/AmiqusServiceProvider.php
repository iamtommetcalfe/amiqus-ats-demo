<?php

namespace App\Providers;

use App\Services\AmiqusOAuthService;
use App\Services\Interfaces\AmiqusOAuthServiceInterface;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AmiqusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register the HTTP client
        $this->app->singleton(Client::class, function ($app) {
            return new Client([
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);
        });

        // Register the AmiqusOAuthService
        $this->app->bind(
            AmiqusOAuthServiceInterface::class,
            AmiqusOAuthService::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
