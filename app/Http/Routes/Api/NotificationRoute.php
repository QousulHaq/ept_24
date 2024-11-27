<?php

namespace App\Http\Routes\Api;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\Api\NotificationController;

class NotificationRoute extends BaseRoute
{
    protected array|string $middleware = ['auth:api'];

    /**
     * Route path prefix.
     *
     * @var string
     */
    protected string $prefix = '/notification';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected string $name = 'notification';

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix(), [
            'as' => $this->name,
            'uses' => $this->uses('index'),
        ]);

        $this->router->post($this->prefix('read-all'), [
            'as' => $this->name('read-all'),
            'uses' => $this->uses('readAll'),
        ]);
    }

    /**
     * Controller used by this route.
     *
     * @return string
     */
    public function controller(): string
    {
        return NotificationController::class;
    }
}
