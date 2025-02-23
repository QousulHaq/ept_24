<?php

namespace App\Http\Routes;

use Illuminate\Http\Request;
use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\DefaultController;

class DefaultRoute extends BaseRoute
{
    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get('/', [
            'as' => 'default',
            'uses' => fn() => view('home'),
        ]);

        $this->router->view('client', 'client');
        
        $this->router->get('client/{any}', function () {
            return view('client');
        })->where('any', '.*');
        

        if (config('app.env') === 'local') {
            $this->router->get('debug', function (Request $request) {
                return $request->ips();
            });
        }
    }

    /**
     * Controller used by this route.
     *
     * @return string
     */
    public function controller(): string
    {
        return DefaultController::class;
    }
}
