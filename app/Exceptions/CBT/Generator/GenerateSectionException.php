<?php

namespace App\Exceptions\CBT\Generator;

use Throwable;
use App\Exceptions\CBT\BaseException;

class GenerateSectionException extends BaseException
{
    public function __construct($process = '', $reason = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct((config('app.debug') ? "[$process] " . (string)$reason : 'Invalid package, please check package warning.'), $code, $previous);
    }
}
