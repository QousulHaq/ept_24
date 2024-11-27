<?php

namespace App\Http\Routes\Api\Client\Section;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\Api\Client\Section\ItemController;

class ItemRoute extends BaseRoute
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
    protected string $prefix = '/client/section/{participant_section}/item/{section_item}';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected string $name = 'client.section.item';

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->put($this->prefix('attempt/{item_attempt}'), [
            'as' => $this->name('attempt'),
            'uses' => $this->uses('attempt'),
        ]);

        $this->router->patch($this->prefix('tick'), [
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
        return ItemController::class;
    }
}
