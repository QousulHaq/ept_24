<?php

namespace App\Entities\CBT\Participant\Section\Item;

use Illuminate\Database\Eloquent\Model;
use App\Entities\CBT\Participant\Section\Item;
use App\Extra\Eloquent\Concerns\UuidAsPrimaryKey;

/**
 * @property-read Item item
 * @property int attempt_number
 */
class Attempt extends Model
{
    use UuidAsPrimaryKey;

    public $incrementing = false;

    protected $table = 'participant_section_item_attempts';

    protected $keyType = 'string';

    protected $fillable = ['participant_item_id', 'attempt_number', 'answer'];

    protected $dates = ['logged_in'];

    protected $hidden = [
        'participant_item_id', 'is_correct', 'score',
        'created_at', 'updated_at', 'participant_section_item_id',
    ];

    protected $casts = [
        'answer' => 'string',
        'score' => 'double',
        'is_correct' => 'boolean',
    ];

    public function item(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Item::class, 'participant_section_item_id', 'id');
    }
}
