<?php

namespace App\Extra;

use App\Entities\Question\Package;
use App\Exceptions\Distribution\FailedDecryptSecret;
use App\Extra\Distribution\Connector;
use App\Extra\Distribution\Decorator;
use App\Extra\Distribution\Encryptor;
use Illuminate\Support\Arr;
use Spatie\Crypto\Rsa\PublicKey;

class Distribution
{
    public function getConnector(array $options = []): Distribution\Connector
    {
        $baseUri = Arr::get($options, 'base_uri');
        $clientId = Arr::get($options, 'client_id');
        $clientSecret = Arr::get($options, 'client_secret');
        $oauthUrl = Arr::get($options, 'oauth_url', '/oauth/token');

        return new Connector($baseUri, $clientId, $clientSecret, $oauthUrl);
    }

    public function getEncryptor(string $secretKey): Distribution\Encryptor
    {
        return new Encryptor($secretKey);
    }

    /**
     * @throws FailedDecryptSecret
     * @throws \Spatie\Crypto\Rsa\Exceptions\CouldNotDecryptData
     */
    public function from(Package $package): Decorator
    {
        return new Decorator(
            $package->id,
            $this->getConnector($package->distribution_options),
            $this->getEncryptorFromAsymmetric($package->distribution_options));
    }

    /**
     * @throws FailedDecryptSecret
     * @throws \Spatie\Crypto\Rsa\Exceptions\CouldNotDecryptData
     */
    private function decryptSecretKey(array $options): string
    {
        $publicKey = PublicKey::fromString($options['public_key']);

        $secret = base64_decode($options['secret']);

        if (! $publicKey->canDecrypt($secret)) {
            throw new FailedDecryptSecret();
        }

        return $publicKey->decrypt($secret);
    }

    /**
     * @throws \Spatie\Crypto\Rsa\Exceptions\CouldNotDecryptData
     * @throws FailedDecryptSecret
     */
    public function getEncryptorFromAsymmetric(array $options): ?Encryptor
    {
        if ($encryptor = Encryptor::fromCache($options['package_id'])) {
            return $encryptor;
        }

        $encryptor = new Encryptor($this->decryptSecretKey($options));

        Encryptor::setCache($options['package_id'], $encryptor);

        return $encryptor;
    }
}
