<?php

namespace App\Http\Routes\BackOffice;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\BackOffice\DashboardController;

class DashboardRoute extends BaseRoute
{
    /**
     * Route path prefix.
     *
     * @var string
     */
    protected string $prefix = 'dashboard';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected string $name = 'dashboard';

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
    }

    /**
     * Controller used by this route.
     *
     * @return string
     */
    public function controller(): string
    {
        return DashboardController::class;
    }
}
