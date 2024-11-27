<?php

namespace App\Entities\CBT;

use App\Entities\Account\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Auditable;
use App\Entities\Question\Package;
use Illuminate\Database\Eloquent\Model;
use App\Extra\Eloquent\Concerns\UuidAsPrimaryKey;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property Package package
 * @property \Illuminate\Database\Eloquent\Collection participants
 * @property string id
 * @property string name
 * @property \Carbon\Carbon $started_at
 * @property \Carbon\Carbon $ended_at
 * @property \Carbon\Carbon $scheduled_at
 * @property Participant|null detail
 */
class Exam extends Model implements AuditableContract
{
    use UuidAsPrimaryKey, Auditable, HasFactory;

    public $incrementing = false;

    protected $table = 'exams';

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'scheduled_at',
        'ended_at',
        'started_at',
        'duration',
        'is_anytime',
        'is_multi_attempt',
        'automatic_start',
        'package_id',
    ];

    protected $hidden = [
        'user_id',
        'exam_id',
    ];

    protected $dates = [
        'scheduled_at',
        'ended_at',
        'started_at',
    ];

    public function package(): Relations\BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    public function participants(): Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants', 'exam_id', 'user_id')
            ->withPivot(['id', 'status'])
            ->using(Participant::class)
            ->as('detail')
            ->withTrashed();
    }
}
