<?php

namespace App\Extra\Contracts;

interface HasRemainingTime
{
    public function getRemainingTime(): int;

    public function decrementRemainingTime(int $amount = 1): int;
}
