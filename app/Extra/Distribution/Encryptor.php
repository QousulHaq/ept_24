<?php

namespace App\Extra\Distribution;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;

class Encryptor implements \Serializable
{
    public const CACHE_PREFIX = 'encrypt:';

    private Encrypter $laravelEncrypter;
    private string $key;

    public function __construct(string $key) {
        $this->key = $key;

        $this->initLaravelEncrypter();
    }

    protected function parseKey(string $key): string
    {
        if (Str::startsWith($key, $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        return $key;
    }

    public function encrypt(string $content): string
    {
        return $this->laravelEncrypter->encryptString($content);
    }

    public function decrypt(string $encryptedContent): string
    {
        return $this->laravelEncrypter->decryptString($encryptedContent);
    }

    public function serialize(): bool|string|null
    {
        return json_encode(['key' => encrypt($this->key)], JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \JsonException
     */
    public function unserialize($data)
    {
        $this->key = decrypt(json_decode($data, true, 512, JSON_THROW_ON_ERROR)['key']);

        $this->initLaravelEncrypter();
    }

    private function initLaravelEncrypter(): void
    {
        $this->laravelEncrypter = new Encrypter($this->parseKey($this->key), config('app.cipher'));
    }

    public static function fromCache(string $identifier): ?self
    {
        return cache()->get(self::CACHE_PREFIX.$identifier);
    }

    public static function cacheExists(string $identifier): bool
    {
        return cache()->has(self::CACHE_PREFIX.$identifier);
    }

    public static function setCache(string $identifier, Encryptor $encryptor): void
    {
        cache()->set(self::CACHE_PREFIX.$identifier, $encryptor, now()->addHours(4));
    }
}
