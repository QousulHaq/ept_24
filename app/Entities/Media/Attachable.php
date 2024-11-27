<?php

namespace App\Entities\Media;

use App\Extra\Eloquent\Concerns\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

/**
 * @property string attachable_uuid
 * @property string attachable_type
 */
class Attachable extends MorphPivot
{
    use UuidAsPrimaryKey;

    public $incrementing = false;

    protected $table = 'attachable';

    protected $keyType = 'string';

    protected $fillable = ['order'];
}
