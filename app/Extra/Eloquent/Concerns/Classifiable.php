<?php

namespace App\Extra\Eloquent\Concerns;

use App\Entities\Classification;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property \Illuminate\Database\Eloquent\Collection classifications
 */
trait Classifiable
{
    public function classifications(): MorphToMany
    {
        return $this->morphToMany(Classification::class, 'classifiable', 'classifiable');
    }

    public function getClassificationAttribute()
    {
        return $this->classifications()->firstOr(['*'], fn () => new Classification());
    }
}
