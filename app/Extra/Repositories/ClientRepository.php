<?php

namespace App\Extra\Repositories;

use App\Entities\Passport\Client;
use App\Entities\Question\Package;
use Illuminate\Http\Request;
use Laravel\Passport\Token;
use Lcobucci\JWT\Configuration;

class ClientRepository
{
    /**
     * @var \Illuminate\Http\Request
     */
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getClient(): Client
    {
        $token = $this->request->bearerToken();

        $tokenId = Configuration::forUnsecuredSigner()->parser()->parse($token)->claims()->get('jti');

        $token = Token::query()->findOrFail($tokenId);

        return $token->client;
    }

    public function getPublicKeyFor(Package $package): ?string
    {
        return $this->getClient()->packages()->where('id', $package->id)->first()?->client_share?->public_key;
    }

    public function getPrivateKeyFor(Package $package): ?string
    {
        return $this->getClient()->packages()->where('id', $package->id)->first()?->client_share?->private_key;
    }

    public function getDistributedPropertyFor(Package $package, string $property): ?string
    {
        return $this->getClient()->packages()->where('id', $package->id)->first()?->client_share?->{$property};
    }
}
