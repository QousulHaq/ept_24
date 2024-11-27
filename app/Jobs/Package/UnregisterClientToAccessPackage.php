<?php

namespace App\Jobs\Package;

use App\Entities\Passport\Client;
use App\Entities\Question\Package;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Crypto\Rsa\KeyPair;

class UnregisterClientToAccessPackage implements Responsable
{
    /**
     * @var \App\Entities\Passport\Client
     */
    private Client $client;

    /**
     * @var \App\Entities\Question\Package
     */
    private Package $package;

    /**
     * RegisterClientToAccessPackage constructor.
     * @param \App\Entities\Passport\Client $client
     * @param \App\Entities\Question\Package $package
     * @throws \Throwable
     */
    public function __construct(Client $client, Package $package)
    {
        throw_if($package->clients()->wherePivot('client_id', $client->id)->doesntExist(),
            ValidationException::withMessages([
                'client' => 'already doesn\'t have access!',
            ]));

        $this->client = $client;
        $this->package = $package;
    }

    public function handle(): void
    {
        $this->client->packages()->detach($this->package->id);
    }

    public function toResponse($request): array
    {
        return [];
    }
}
