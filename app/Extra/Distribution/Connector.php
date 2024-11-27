<?php

namespace App\Extra\Distribution;

use App\Exceptions\Distribution\UnauthorizedFromNode;
use App\Extra\Distribution\Connector\Service;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JetBrains\PhpStorm\Pure;

class Connector
{
    public const CACHE_PREFIX = 'token:';

    /**
     * @var \GuzzleHttp\Client
     */
    private Client $client;

    private Connector\Token $token;

    public function __construct(string $baseUri, string $clientId, string $clientSecret, string $oauthUrl = '/oauth/token')
    {
        $this->client = new Client([
            'base_uri' => $baseUri,
            RequestOptions::TIMEOUT => 2.,
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
        ]);

        $this->resolveToken($clientId, $clientSecret, $oauthUrl);
    }

    private function resolveToken(string $clientId, string $clientSecret, string $oauthUrl = '/oauth/token'): void
    {
        $tokenKey = self::CACHE_PREFIX.base64_encode($clientSecret.$clientSecret);

        $this->token = cache()->get($tokenKey, function () use ($clientId, $clientSecret, $oauthUrl, $tokenKey) {
            $token = $this->getFreshToken($clientId, $clientSecret, $oauthUrl);

            cache()->set($tokenKey, $token, $token->expiresIn);

            return $token;
        });
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $oauthUrl
     * @return \App\Extra\Distribution\Connector\Token
     * @throws \App\Exceptions\Distribution\UnauthorizedFromNode|\JsonException
     */
    private function getFreshToken(string $clientId, string $clientSecret, string $oauthUrl = '/oauth/token'): Connector\Token
    {
        try {
            $response = $this->client->post($oauthUrl, [
                RequestOptions::JSON => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            return new Connector\Token($data);
        } catch (GuzzleException $e) {
            throw new UnauthorizedFromNode(previous: $e);
        }
    }

    #[Pure] public function getService(): Service
    {
        return new Connector\Service($this->client, $this->token);
    }
}
