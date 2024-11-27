<?php

namespace App\Http\Controllers\BackOffice\Package;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DistributedController extends Controller
{
    public function create(): View
    {
        return view('pages.package.distributed.create');
    }
}
