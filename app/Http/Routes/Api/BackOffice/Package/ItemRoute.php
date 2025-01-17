<?php

namespace App\Http\Routes\Api\BackOffice\Package;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\Api\BackOffice\Package\ItemController;

class ItemRoute extends BaseRoute
{
    protected string $prefix = 'back-office/package/{package__}/item';

    protected string $name = 'back-office.package.item';

    protected array|string $middleware = ['auth:api', 'role:superuser|proctor'];

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix, [
            'as' => $this->name('index'),
            'uses' => $this->uses('index'),
        ]);

        $this->router->post($this->prefix('store'), [
            'as' => $this->name('store'),
            'uses' => $this->uses('store'),
        ]);

        $this->router->get($this->prefix('{item}'), [
            'as' => $this->name('show'),
            'uses' => $this->uses('show'),
        ]);

        // $this->router->put($this->prefix('{item}/update'), [
        //     'as' => $this->name('update'),
        //     'uses' => $this->uses('update'),
        // ]);

        $this->router->delete($this->prefix('{item}/destroy'), [
            'as' => $this->name('destroy'),
            'uses' => $this->uses('destroy'),
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
