<?php

namespace App\Http\Routes\Api\Client;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\Api\Client\ExamController;

class ExamRoute extends BaseRoute
{
    /**
     * Middleware used in route.
     *
     * @var array|string
     */
    protected array|string $middleware = ['auth:api'];

    /**
     * Route path prefix.
     *
     * @var string
     */
    protected string $prefix = '/client/exam';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected string $name = 'client.exam';

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

        $this->router->post($this->prefix('{exam}/enroll'), [
            'as' => $this->name('enroll'),
            'uses' => $this->uses('enroll'),
        ]);
    }

    /**
     * Controller used by this route.
     *
     * @return string
     */
    public function controller(): string
    {
        return ExamController::class;
    }
}
