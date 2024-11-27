<?php

namespace App\Exceptions\CBT;

use Throwable;

class ConfigNotFoundException extends BaseException
{
    public function __construct($config = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct("Config [$config] not found", $code, $previous);
    }
}
