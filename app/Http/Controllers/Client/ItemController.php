<?php

namespace App\Http\Controllers\Client;

use App\Entities\Question\Package;
use App\Http\Resources\Client\Sync\ItemsEncryptionResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Dentro\Yalr\Attributes\Get;
use Dentro\Yalr\Attributes\Name;
use Dentro\Yalr\Attributes\Prefix;

#[Prefix('package/{package__}/item'), Name('package.item', dotSuffix: true)]
class ItemController
{
    #[Get('/', 'index')]
    public function index(Package $package): JsonResource
    {
        return (new ItemsEncryptionResource($package->allItems()->get(), $package->ancestor));
    }
}
