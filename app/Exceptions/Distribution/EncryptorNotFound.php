<?php

namespace App\Exceptions\Distribution;

use JetBrains\PhpStorm\Pure;
use Throwable;

class EncryptorNotFound extends \Exception
{
    #[Pure]
    public function __construct(
        $message = "encryptor not present in cache. it may caused by passphrase requirement.",
        $code = 0,
        Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
