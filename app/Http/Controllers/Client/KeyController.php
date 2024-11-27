<?php

namespace App\Http\Controllers\Client;

use App\Entities\Question\Package;
use App\Extra\Repositories\ClientRepository;
use App\Http\Controllers\Controller;
use Dentro\Yalr\Attributes\Get;
use Dentro\Yalr\Attributes\Name;
use Dentro\Yalr\Attributes\Prefix;
use Spatie\Crypto\Rsa\PrivateKey;

#[Prefix('key'), Name('key', dotSuffix: true)]
class KeyController extends Controller
{
    #[Get('', name: 'index')]
    public function index(ClientRepository $repository): \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
    {
        $client = $repository->getClient();

        return $client->packages->map(function(Package $package) {
            $privateKey = PrivateKey::fromString($package->client_share->private_key, $package->client_share->passphrase);

            return [
                'name' => $package->title,
                'id' => $package->id,
                'public_key' => $package->client_share->public_key,
                'secret' => base64_encode($privateKey->encrypt($package->client_share->secret)),
                'has_passphrase' => (bool) $package->client_share->passphrase,
            ];
        });
    }
}
