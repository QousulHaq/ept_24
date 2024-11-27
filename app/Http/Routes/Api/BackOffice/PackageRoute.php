<?php

namespace App\Http\Routes\Api\BackOffice;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\Api\BackOffice\PackageController;

class PackageRoute extends BaseRoute
{
    protected string $prefix = 'back-office/package/{package__}';

    protected string $name = 'back-office.package';

    protected array|string $middleware = ['auth:api', 'role:superuser|proctor'];

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix('show'), [
            'as' => $this->name('show'),
            'uses' => $this->uses('show'),
        ]);

        $this->router->get($this->prefix, [
            'as' => $this->name('index'),
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
        return PackageController::class;
    }
}
