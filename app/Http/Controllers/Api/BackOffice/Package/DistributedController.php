<?php

namespace App\Http\Controllers\Api\BackOffice\Package;

use App\Extra\Distribution;
use App\Http\Controllers\Controller;
use App\Jobs\Distribution\SyncDistributionPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Dentro\Yalr\Attributes\Get;
use Dentro\Yalr\Attributes\Middleware;
use Dentro\Yalr\Attributes\Name;
use Dentro\Yalr\Attributes\Post;
use Dentro\Yalr\Attributes\Prefix;

#[Prefix('back-office/package/distributed'), Name('back-office.package.distributed.'), Middleware(['auth:api', 'role:superuser'])]
class DistributedController extends Controller
{
    #[Get('shareable', name: 'shareable')]
    public function shareable(Request $request, Distribution $distribution): JsonResponse
    {
        $connector = $distribution->getConnector($request->validate([
            'base_uri' => 'required',
            'client_id' => 'required',
            'client_secret' => 'required',
        ]));

        return response()->json($connector->getService()->getShareable());
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    #[Post('', name: 'store')]
    public function store(Request $request): JsonResponse
    {
        $bus = Bus::batch([
            new SyncDistributionPackage($request->all()),
        ])->name('sync distribution package');

        $bus->dispatch();

        return response()->json([
            'message' => 'package load in background',
        ]);
    }
}
