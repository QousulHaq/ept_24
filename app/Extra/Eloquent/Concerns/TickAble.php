<?php

namespace App\Extra\Eloquent\Concerns;

trait TickAble
{
    public function getRemainingTime(): int
    {
        return $this->getAttribute('remaining_time');
    }

    public function decrementRemainingTime(int $amount = 1): int
    {
        return $this->decrement('remaining_time', $amount);
    }
}
