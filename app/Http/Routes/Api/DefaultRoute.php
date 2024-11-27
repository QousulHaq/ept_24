<?php

namespace App\Http\Routes\Api;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\Api\DefaultController;

class DefaultRoute extends BaseRoute
{
    protected array|string $middleware = ['auth:api'];

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get('/', [
            'as' => 'default',
            'uses' => $this->uses('index'),
        ]);

        $this->router->get('me', [
            'as' => 'user',
            'uses' => $this->uses('me'),
        ]);

        $this->router->get('client/me', [
            'as' => 'client.user',
            'uses' => $this->uses('me'),
        ]);
    }

    public function controller(): string
    {
        return DefaultController::class;
    }
}
