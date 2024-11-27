<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use App\Http\Controllers\Controller;

class DefaultController extends Controller
{
    public function index(Request $request, Router $router)
    {
        return collect($router->getRoutes()->getRoutes())
            ->filter(function (Route $route) use ($request) {
                /**
                 * @var $user \App\Entities\Account\User
                 */
                $user = $request->user();

                return Str::startsWith($route->getName(),
                    $user->isA('student') && $user->getRoles()->count() == 1 ? 'api.client' : 'api');
            })->map(fn (Route $route) => [
                'name' => $route->getName(),
                'domain' => $route->getDomain(),
                'uri' => $route->uri(),
                'method' => $route->methods()[0],
            ])->values();
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
