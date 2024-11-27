<?php

namespace App\Entities\CBT\Participant;

use Illuminate\Support\Arr;
use App\Entities\CBT\Participant;
use Illuminate\Database\Eloquent\Model;
use App\Extra\Contracts\HasRemainingTime;
use App\Extra\Eloquent\Concerns\TickAble;
use App\Extra\Eloquent\Concerns\HasConfigCBT;
use App\Extra\Eloquent\Concerns\UuidAsPrimaryKey;

/**
 * Class Section.
 *
 * @property-read \Illuminate\Database\Eloquent\Collection $items
 * @property-read Participant participant
 * @property int score
 * @property bool item_duration
 * @property int attempts
 * @property string participant_id
 */
class Section extends Model implements HasRemainingTime
{
    use UuidAsPrimaryKey, TickAble, HasConfigCBT {
        getConfigAttribute as parentGetConfigAttribute;
    }

    public $incrementing = false;

    protected $table = 'participant_sections';

    protected $keyType = 'string';

    protected $fillable = [
        'last_attempted_at',
        'ended_at',
        'item_duration',
        'participant_id',
        'remaining_time',
        'attempts',
        'score',
        'config',
    ];

    protected $hidden = [
        'score', 'participant_id', 'created_at', 'updated_at',
    ];

    protected $appends = [
        'config',
    ];

    protected $casts = [
        'remaining_time' => 'int',
        'item_duration' => 'boolean',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(Section\Item::class, 'section_id', 'id');
    }

    public function incrementAttempts(): bool|int
    {
        return $this->increment('attempts');
    }

    public function getConfigAttribute(): ?array
    {
        return Arr::except($this->parentGetConfigAttribute(), ['sub-preset', 'item']);
    }
}
