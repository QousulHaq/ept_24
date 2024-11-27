<?php

namespace App\Providers;

use App\Extra\Repositories\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class ClientResolverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(ClientRepository::class, fn() => new ClientRepository($this->app->make(Request::class)));
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
