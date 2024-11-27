<?php

namespace App\Http\Routes\Api\BackOffice;

use App\Http\Controllers\Api\BackOffice\ItemController;
use Dentro\Yalr\BaseRoute;

class ItemRoute extends BaseRoute
{
    protected string $prefix = 'back-office/item';

    protected string $name = 'back-office.item';

    public function register(): void
    {
        $this->router->get($this->prefix, [
            'as' => $this->name('index'),
            'uses' => $this->uses('index'),
        ]);

        $this->router->patch($this->prefix('{package__}/attach'), [
            'as' => $this->name('attach'),
            'uses' => $this->uses('attach'),
        ]);
    }

    public function controller(): string
    {
        return ItemController::class;
    }
}
