<?php

namespace App\Providers;

use App\Services\AmiqusApiLogService;
use App\Services\AmiqusApiService;
use App\Services\AmiqusAuthService;
use App\Services\AmiqusClientService;
use App\Services\AmiqusCredentialService;
use App\Services\AmiqusOAuthService;
use App\Services\AmiqusTokenService;
use App\Services\Interfaces\AmiqusApiLogServiceInterface;
use App\Services\Interfaces\AmiqusApiServiceInterface;
use App\Services\Interfaces\AmiqusAuthServiceInterface;
use App\Services\Interfaces\AmiqusClientServiceInterface;
use App\Services\Interfaces\AmiqusCredentialServiceInterface;
use App\Services\Interfaces\AmiqusOAuthServiceInterface;
use App\Services\Interfaces\AmiqusTokenServiceInterface;
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

        // Register the credential service
        $this->app->bind(
            AmiqusCredentialServiceInterface::class,
            AmiqusCredentialService::class
        );

        // Register the auth service
        $this->app->bind(
            AmiqusAuthServiceInterface::class,
            AmiqusAuthService::class
        );

        // Register the token service
        $this->app->bind(
            AmiqusTokenServiceInterface::class,
            AmiqusTokenService::class
        );

        // Register the API service
        $this->app->bind(
            AmiqusApiServiceInterface::class,
            AmiqusApiService::class
        );

        // Register the AmiqusOAuthService
        $this->app->bind(
            AmiqusOAuthServiceInterface::class,
            AmiqusOAuthService::class
        );

        // Register the AmiqusClientService
        $this->app->bind(
            AmiqusClientServiceInterface::class,
            AmiqusClientService::class
        );

        // Register the AmiqusApiLogService
        $this->app->bind(
            AmiqusApiLogServiceInterface::class,
            AmiqusApiLogService::class
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
