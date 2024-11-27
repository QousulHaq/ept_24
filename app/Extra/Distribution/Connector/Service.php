<?php

namespace App\Extra\Distribution\Connector;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Service
{
    public function __construct(
        private Client $client,
        private Token $token)
    {}

    public function getShareable()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $this->client->get('api/v1/package', [
            RequestOptions::HEADERS => [
                'Authorization' => $this->token->tokenType.' '.$this->token->accessToken,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public function getCompositeKeys(?string $packageId = null): array|string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $this->client->get('api/v1/key', [
            RequestOptions::HEADERS => [
                'Authorization' => $this->token->tokenType.' '.$this->token->accessToken,
            ],
        ]);

        $data = collect(json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR));

        if ($packageId && $data->some('id', '=', $packageId)) {
            return $data->firstWhere('id', '=', $packageId);
        }

        return $data->toArray();
    }

    public function getPackageDetail(string $packageId): array
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $this->client->get('api/v1/package/'.$packageId, [
            RequestOptions::HEADERS => [
                'Authorization' => $this->token->tokenType.' '.$this->token->accessToken,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR)['data'];
    }

    public function getItems(string $packageId): array
    {
        $response = $this->client->get('api/v1/package/'.$packageId.'/item', [
            RequestOptions::HEADERS => [
                'Authorization' => $this->token->tokenType.' '.$this->token->accessToken,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR)['data'];
    }
}
