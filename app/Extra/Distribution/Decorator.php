<?php

namespace App\Extra\Distribution;

use App\Exceptions\Distribution\EncryptorNotFound;

class Decorator
{
    public function __construct(
        public string $identifier,
        public Connector $connector,
        protected ?Encryptor $encryptor = null,
    ) {}

    /**
     * @return Encryptor
     * @throws EncryptorNotFound
     */
    public function getEncryptor(): Encryptor
    {
        if (! $this->encryptor) {
            if ($this->encryptor = Encryptor::fromCache($this->identifier)) {
                return $this->encryptor;
            }

            throw new EncryptorNotFound();
        }

        return $this->encryptor;
    }

    /**
     * @param Encryptor $encryptor
     */
    public function setEncryptor(Encryptor $encryptor): void
    {
        $this->encryptor = $encryptor;
    }
}
