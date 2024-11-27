<?php

namespace App\Http\Controllers\Api\BackOffice;

use App\Entities\Passport\Client;
use App\Entities\Question\Package;
use App\Http\Controllers\Controller;
use App\Jobs\Package\RegisterClientToAccessPackage;
use App\Jobs\Package\UnregisterClientToAccessPackage;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Dentro\Yalr\Attributes\Get;
use Dentro\Yalr\Attributes\Middleware;
use Dentro\Yalr\Attributes\Name;
use Dentro\Yalr\Attributes\Patch;
use Dentro\Yalr\Attributes\Prefix;

#[Prefix('back-office/package/{package__}'), Name('back-office.package', dotSuffix: true), Middleware(['auth:api', 'role:superuser|proctor'])]
class PackageController extends Controller
{
    #[Get('', name: 'show')]
    public function show(Package $package): Package
    {
        return $package->fresh(['categories']);
    }

    #[Patch('/{client}/share', name: 'share')]
    public function share(Request $request, Package $package, Client $client): Responsable
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $job = new RegisterClientToAccessPackage($client, $package, $request->all());

        $this->dispatchNow($job);

        return $job;
    }

    #[Patch('/{client}/unshared', name: 'unshared')]
    public function unshared(Package $package, Client $client): Responsable
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $job = new UnregisterClientToAccessPackage($client, $package);

        $this->dispatchNow($job);

        return $job;
    }
}
