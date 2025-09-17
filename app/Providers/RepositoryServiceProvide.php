<?php

namespace App\Providers;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Elquent\UserRepository;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;
use App\Services\Interface\UserServiceInterface;

class RepositoryServiceProvide extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Repository
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        // Bind Service
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
