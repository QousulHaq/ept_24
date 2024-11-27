<?php

namespace App\Http\Controllers;

use App\Entities\Account\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function authenticatedUser(): User
    {
        /* @noinspection PhpIncompatibleReturnTypeInspection */
        return auth()->user();
    }
}
