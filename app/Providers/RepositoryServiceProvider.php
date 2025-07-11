<?php

namespace App\Providers;

use App\Repositories\AmiqusOAuthAccessTokenRepository;
use App\Repositories\AmiqusOAuthClientRepository;
use App\Repositories\AmiqusOAuthRefreshTokenRepository;
use App\Repositories\Interfaces\AmiqusOAuthAccessTokenRepositoryInterface;
use App\Repositories\Interfaces\AmiqusOAuthClientRepositoryInterface;
use App\Repositories\Interfaces\AmiqusOAuthRefreshTokenRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            AmiqusOAuthClientRepositoryInterface::class,
            AmiqusOAuthClientRepository::class
        );

        $this->app->bind(
            AmiqusOAuthAccessTokenRepositoryInterface::class,
            AmiqusOAuthAccessTokenRepository::class
        );

        $this->app->bind(
            AmiqusOAuthRefreshTokenRepositoryInterface::class,
            AmiqusOAuthRefreshTokenRepository::class
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
