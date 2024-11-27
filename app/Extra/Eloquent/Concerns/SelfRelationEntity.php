<?php

namespace App\Extra\Eloquent\Concerns;

use App\Extra\Eloquent\Scopes\RootEntityScope;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait SelfRelationEntity.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection $children
 * @property-read  mixed $ancestor
 * @property string parent_id
 */
trait SelfRelationEntity
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id')
            ->withoutGlobalScopes([RootEntityScope::class]);
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->withoutGlobalScopes([RootEntityScope::class]);
    }

    /** @noinspection PhpUnused */
    public static function bootSelfRelationEntity()
    {
        self::addGlobalScope(new RootEntityScope);

        self::deleting(function (self $model) {
            /* @noinspection PhpUndefinedMethodInspection */
            $model->children->each->delete();
        });
    }

    public function getAncestorAttribute(): self
    {
        if (is_null($this->parent_id)) {
            return $this;
        }

        return $this->parent->ancestor;
    }

    public function findDescendant(string $id, self $package = null): ?self
    {
        $package = is_null($package) ? $this : $package;
        foreach ($package->children as $child) {
            if ($child->id === $id) {
                return $child;
            }
            if ($child->children->count() > 0) {
                $find = $package->findDescendant($id, $child);
                if (! is_null($find)) {
                    return $find;
                }
            }
        }

        return null;
    }
}
