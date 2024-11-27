<?php

namespace App\Entities\CBT;

use App\Entities\Account\User;
use App\Entities\CBT\Participant\Section;
use App\Entities\Question\Package;
use App\Extra\Eloquent\Concerns\UuidAsPrimaryKey;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection sections
 * @property-read User user
 * @property string exam_id
 * @property-read Exam exam
 * @property-read Package package
 * @property string status
 */
class Participant extends Pivot
{
    use UuidAsPrimaryKey;

    public const STATUS_NOT_READY = 'not ready';
    public const STATUS_READY = 'ready';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BANNED = 'banned';
    public const STATUS_FINISHED = 'finished';

    // const status below is for development of server push ticking
    const STATUS_AWAY = 'away';
    const STATUS_JOINING = 'joining';

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'participants';

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'exam_id',
        'status',
    ];

    protected $hidden = [
        'package'
    ];

    public function user(): Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function exam(): Relations\BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'id');
    }

    public function sections(): Relations\HasMany
    {
        return $this->hasMany(Section::class, 'participant_id', 'id');
    }

    public function package(): Relations\HasOneThrough
    {
        return $this->hasOneThrough(Package::class, Exam::class,
            'id', 'id',
            'exam_id', 'package_id')
            ->without(['children']);
    }

    public function items(): Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Participant\Section\Item::class,
            Participant\Section::class,
            'participant_id',
            'section_id'
        );
    }

    public function logs(): Relations\HasMany
    {
        return $this
            ->hasMany(Participant\Log::class, 'participant_id', 'id')
            ->orderByDesc('participant_logs.id');
    }

    public function getScoreAttribute(): float
    {
        return $this->package->preset->getScore($this);
    }
}
