<?php

namespace App\Jobs\Package;

use App\Entities\Passport\Client;
use App\Entities\Question\Package;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Crypto\Rsa\KeyPair;

class RegisterClientToAccessPackage implements Responsable
{
    /**
     * @var \App\Entities\Passport\Client
     */
    private Client $client;

    /**
     * @var \App\Entities\Question\Package
     */
    private Package $package;

    private array $inputs;

    /**
     * RegisterClientToAccessPackage constructor.
     * @param \App\Entities\Passport\Client $client
     * @param \App\Entities\Question\Package $package
     * @param array $inputs
     * @throws \Illuminate\Validation\ValidationException|\Throwable
     */
    public function __construct(Client $client, Package $package, array $inputs = [])
    {
        throw_if($package->clients()->wherePivot('client_id', $client->id)->exists(),
            ValidationException::withMessages([
                'package' => 'already have an access!',
            ]));

        $this->client = $client;
        $this->package = $package;

        $this->inputs = Validator::make($inputs, [
            'passphrase' => 'nullable|min:8',
        ])->validated();
    }

    public function handle(): void
    {
        $keyPair = new KeyPair(privateKeyType: OPENSSL_KEYTYPE_RSA);

        if (array_key_exists('passphrase', $this->inputs)) {
            $keyPair->password($this->inputs['passphrase']);
        }

        [$privateKey, $publicKey] = $keyPair->generate();

        $this->client->packages()->attach($this->package, [
            'public_key' => $publicKey,
            'private_key' => $privateKey,
            'passphrase' => $this->inputs['passphrase'] ?? null,
            'secret' => $this->generateRandomKey(),
        ]);
    }

    protected function generateRandomKey(): string
    {
        return 'base64:'.base64_encode(Encrypter::generateKey(config('app.cipher')));
    }

    public function toResponse($request): mixed
    {
        return $this->client->packages()->where('id', $this->package->id)->first()->client_share;
    }
}
