<?php

namespace App\Http\Routes\Api\Client;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\Api\Client\SectionController;

class SectionRoute extends BaseRoute
{
    /**
     * Middleware used in route.
     *
     * @var array|string
     */
    protected array|string $middleware = ['auth:api', 'signature'];

    /**
     * Route path prefix.
     *
     * @var string
     */
    protected string $prefix = '/client/section';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected string $name = 'client.section';

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix, [
            'as' => $this->name,
            'uses' => $this->uses('index'),
        ]);

        $this->router->get($this->prefix('/{participant_section}'), [
            'as' => $this->name('show'),
            'uses' => $this->uses('show'),
        ]);

        $this->router->post($this->prefix('/{participant_section}/start'), [
            'as' => $this->name('start'),
            'uses' => $this->uses('start'),
        ]);

        $this->router->patch($this->prefix('/{participant_section}/tick'), [
            'as' => $this->name('tick'),
            'uses' => $this->uses('tick'),
        ]);
    }

    /**
     * Controller used by this route.
     *
     * @return string
     */
    public function controller(): string
    {
        return SectionController::class;
    }
}
