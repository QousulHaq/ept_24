<?php

namespace App\Http\Routes\BackOffice\Package;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\BackOffice\Package\ItemController;

class ItemRoute extends BaseRoute
{
    protected string $prefix = 'package/{package}/item/';

    protected string $name = 'package.item';

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix('create'), [
            'as' => $this->name('create'),
            'uses' => $this->uses('create'),
        ]);

        $this->router->get($this->prefix('/{item}/item'), [
            'as' => $this->name('edit'),
            'uses' => $this->uses('edit'),
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
