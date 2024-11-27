<?php

namespace App\Extra\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait GlobalQueryOrder
{
    public static string $SCOPE_NAME = 'ORDER_SCOPE';

    protected static ?string $order_direction = null;

    public static function bootGlobalQueryOrder()
    {
        self::addGlobalScope(self::$SCOPE_NAME, function (Builder $builder) {
            $builder->orderBy(self::orderByColumn(), self::orderDirection());
        });
    }

    protected static function orderByColumn(): string
    {
        return 'updated_at';
    }

    protected static function orderDirection(): string
    {
        return 'desc';
    }
}
