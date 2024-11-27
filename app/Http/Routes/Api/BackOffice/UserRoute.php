<?php

namespace App\Http\Routes\Api\BackOffice;

use App\Http\Controllers\Api\BackOffice\UserController;
use Dentro\Yalr\BaseRoute;

class UserRoute extends BaseRoute
{
    protected string $prefix = 'back-office/user';

    protected string $name = 'back-office.user';

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix('participant'), [
            'as' => $this->name('participant'),
            'uses' => $this->uses('participant'),
        ]);
    }

    public function controller(): string
    {
        return UserController::class;
    }
}
