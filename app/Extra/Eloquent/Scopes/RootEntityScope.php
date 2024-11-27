<?php

namespace App\Extra\Eloquent\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class RootEntityScope implements Scope
{
    /**
     * {@inheritdoc}
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereNull('parent_id');
    }
}
