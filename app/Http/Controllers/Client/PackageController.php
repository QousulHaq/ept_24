<?php

namespace App\Http\Controllers\Client;

use App\Entities\Question\Package;
use App\Extra\Repositories\ClientRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\Sync\PackageResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Dentro\Yalr\Attributes\Get;
use Dentro\Yalr\Attributes\Name;
use Dentro\Yalr\Attributes\Prefix;

#[Prefix('package'), Name('package', dotSuffix: true)]
class PackageController extends Controller
{
    #[Get('', 'index')]
    public function index(ClientRepository $repository): JsonResource
    {
        return PackageResource::collection($repository->getClient()->packages);
    }

    #[Get('{package__}', name: 'show')]
    public function show(Package $package): JsonResource
    {
        return PackageResource::make($package);
    }
}
