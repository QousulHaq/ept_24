<?php

namespace App\Http\Routes\BackOffice;

use App\Http\Controllers\BackOffice\Package\DistributedController;
use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\BackOffice\PackageController;

class PackageRoute extends BaseRoute
{
    /**
     * Route path prefix.
     *
     * @var string
     */
    protected string $prefix = 'package';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected string $name = 'package';

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix('create-distributed'), [
            'as' => $this->name('distributed.create'),
            'uses' => $this->uses('create', DistributedController::class),
        ]);

        $this->router->post($this->prefix('create-distributed'), [
            'as' => $this->name('distributed.store'),
            'uses' => $this->uses('store', DistributedController::class),
        ]);

        $this->router->get($this->prefix('distributed/shareable'), [
            'as' => $this->name('distributed.shareable'),
            'uses' => $this->uses('shareable', DistributedController::class),
        ]);

        $this->router->resource($this->prefix, $this->controller());
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
