<?php

namespace App\Http\Routes;

use Dentro\Yalr\BaseRoute;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

class AuthRoute extends BaseRoute
{
    /**
     * Route path prefix.
     *
     * @var string
     */
    protected string $prefix = 'auth';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected string $name = 'auth';

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix('login'), [
            'as' => $this->name('showLogin'),
            'uses' => $this->uses('create', AuthenticatedSessionController::class),
        ])->middleware('guest');

        $this->router->post($this->prefix('login'), [
            'as' => $this->name('login'),
            'uses' => $this->uses('store', AuthenticatedSessionController::class),
        ])->middleware('guest');

        $this->router->get($this->prefix('register'), [
            'as' => $this->name('create'),
            'uses' => $this->uses('create', RegisteredUserController::class),
        ])->middleware('guest');

        $this->router->post($this->prefix('register'), [
            'as' => $this->name('register'),
            'uses' => $this->uses('store', RegisteredUserController::class),
        ])->middleware('guest');

        $this->router->post($this->prefix('logout'), [
            'as' => $this->name('logout'),
            'uses' => $this->uses('destroy', AuthenticatedSessionController::class),
        ]);
    }
}
