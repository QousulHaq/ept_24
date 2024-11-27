<?php

namespace App\Entities\CBT\Participant;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property  Carbon created_at
 */
class Log extends Model
{
    use HasFactory;

    protected $table = 'participant_logs';

    protected $fillable = [
        'content',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    protected $appends = [
        'diff_time',
    ];

    public function getDiffTimeAttribute(): string
    {
        return now()->longRelativeDiffForHumans($this->created_at);
    }
}
