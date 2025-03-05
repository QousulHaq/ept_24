<?php

namespace App\Extra\Repositories;

use Lcobucci\JWT\Configuration;
use Illuminate\Validation\UnauthorizedException;
use Lcobucci\JWT\Token as TokenJwt;
use Lcobucci\JWT\Token\Plain as PlainJwtToken;

class TokenRepository
{
    public static function generate($jti, ?\DateTimeImmutable $expiresAt = null): PlainJwtToken
    {
        if ($expiresAt === null) {
            $expiresAt = now()->addHours(5)->toDateTimeImmutable();
        }

        /** @var Configuration $config */
        $config = app('cbt.jwt.token.config');

        return $config->builder()
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy($jti)
            ->issuedAt(now()->toDateTimeImmutable())
            ->expiresAt($expiresAt)
            ->withClaim('uid', 1)
            ->getToken($config->signer(), $config->signingKey());
    }

    public static function parse(string $token): TokenJwt
    {
        try {
            /** @var Configuration $config */
            $config = app('cbt.jwt.token.config');

            return $config->parser()->parse((string) $token);
        } catch (\Exception $e) {
            throw new UnauthorizedException('Failed to fetch token', 412, $e);
        }
    }
}
