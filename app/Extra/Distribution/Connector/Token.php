<?php

namespace App\Extra\Distribution\Connector;

use Carbon\Carbon;

class Token
{
    public string $tokenType;

    public Carbon $expiresIn;

    public string $accessToken;

    public function __construct(array $data)
    {
        $this->tokenType = $data['token_type'];
        $this->expiresIn = now()->addSeconds($data['expires_in']);
        $this->accessToken = $data['access_token'];
    }
}
