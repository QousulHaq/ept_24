<?php

namespace App\Http\Routes\BackOffice;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\BackOffice\UserController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;

class UserRoute extends BaseRoute
{
    /**
     * Route path prefix.
     *
     * @var string
     */
    protected string $prefix = 'user/{role}';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected string $name = 'user';

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

        $this->router->post($this->prefix, [
            'as' => $this->name('store'),
            'uses' => $this->uses('store'),
        ]);

        $this->router->get($this->prefix('create'), [
            'as' => $this->name('create'),
            'uses' => $this->uses('create'),
        ]);

        $this->router->get($this->prefix('import'), [
            'as' => $this->name('import'),
            'uses' => $this->uses('import'),
        ]);

        $this->router->post($this->prefix('store-import'), [
            'as' => $this->name('store-import'),
            'uses' => $this->uses('storeImport'),
        ]);

        $this->router->put($this->prefix('{user}'), [
            'as' => $this->name('update'),
            'uses' => $this->uses('update'),
        ]);

        $this->router->delete($this->prefix('{user}'), [
            'as' => $this->name('destroy'),
            'uses' => $this->uses('destroy'),
        ]);

        $this->router->get($this->prefix('{user}/edit'), [
            'as' => $this->name('edit'),
            'uses' => $this->uses('edit'),
        ]);

        $this->router->get($this->prefix('{user}/password'), [
            'as' => $this->name('password'),
            'uses' => $this->uses('editPassword'),
        ]);

        $this->router->post($this->prefix('reset'), [
            'as' => $this->name('reset'),
            'uses' => $this->uses('store', PasswordResetLinkController::class),
        ]);
    }

    public function controller(): string
    {
        return UserController::class;
    }
}
