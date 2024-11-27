<?php

namespace App\Extra\Eloquent\Concerns;

use Illuminate\Support\Str;

trait UuidAsPrimaryKey
{
    /** @noinspection PhpUnused */
    public static function bootUuidAsPrimaryKey(): void
    {
        self::creating(static fn (self $model) => $model->setAttribute('id', (string) Str::orderedUuid()));
    }
}
