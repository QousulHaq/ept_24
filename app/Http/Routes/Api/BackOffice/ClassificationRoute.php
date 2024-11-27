<?php

namespace App\Http\Routes\Api\BackOffice;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\Api\BackOffice\ClassificationController;

class ClassificationRoute extends BaseRoute
{
    protected string $prefix = 'back-office/classification';

    protected string $name = 'back-office.classification';

    protected array|string $middleware = ['auth:api', 'role:superuser|manager'];

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

        $this->router->put($this->prefix('{classification}/update'), [
            'as' => $this->name('update'),
            'uses' => $this->uses('update'),
        ]);

        $this->router->delete($this->prefix('{classification}/destroy'), [
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
        return ClassificationController::class;
    }
}
