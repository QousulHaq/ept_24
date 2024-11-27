<?php

namespace App\Http\Routes\BackOffice;

use Dentro\Yalr\BaseRoute;

class DefaultRoute extends BaseRoute
{
    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->redirect('/back-office', '/back-office/dashboard')->name('back-office');
    }
}
