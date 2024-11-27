<?php

namespace App\Http\Middleware;

use Closure;
use Silber\Bouncer\Bouncer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BouncerRole
{
    /**
     * @var \Silber\Bouncer\Bouncer
     */
    private $bouncer;

    /**
     * @var \App\Entities\Account\User
     */
    private $user;

    /**
     * BouncerRole constructor.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Silber\Bouncer\Bouncer $bouncer
     */
    public function __construct(Request $request, Bouncer $bouncer)
    {
        $this->bouncer = $bouncer;
        $this->user = $request->user();
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $roles
     * @return mixed|void
     * @throws \Throwable
     */
    public function handle($request, Closure $next, $roles = '')
    {
        $roles = explode('|', $roles);

        foreach ($roles as $role) {
            if ($this->user->isA($role)) {
                return $next($request);
            }
        }

        abort(Response::HTTP_UNAUTHORIZED, 'Your roles doesn\'t match with this route');
    }
}
