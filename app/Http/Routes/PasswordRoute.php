<?php

namespace App\Http\Routes;

use Dentro\Yalr\BaseRoute;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;

class PasswordRoute extends BaseRoute
{
    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->post($this->prefix('reset/update'), [
            'as' => 'password.update',
            'uses' => $this->uses('store', NewPasswordController::class),
        ])->middleware('guest');

        $this->router->get($this->prefix('reset/{token}'), [
            'as' => 'password.reset',
            'uses' => $this->uses('create', NewPasswordController::class),
        ])->middleware('guest');

        $this->router->post($this->prefix('reset'), [
            'as' => 'auth.reset',
            'uses' => $this->uses('store', PasswordResetLinkController::class),
        ])->middleware('auth');
    }
}
