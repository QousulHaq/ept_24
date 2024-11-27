<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class Classification extends Model
{
    use SoftDeletes, HashableId {
        getHashAttribute as parentGetHashAttribute;
    }

    const TYPE_CATEGORY = 'category';
    const TYPE_GROUP = 'group';

    protected $table = 'classifications';

    protected $fillable = ['type', 'name'];

    protected $hidden = ['id', 'deleted_at'];

    protected $appends = ['hash'];

    public function getHashAttribute(): string
    {
        if (empty($this->getKey())) {
            return '';
        }

        return $this->parentGetHashAttribute();
    }
}
