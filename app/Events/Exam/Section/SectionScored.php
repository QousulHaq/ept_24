<?php

namespace App\Events\Exam\Section;

use App\Entities\CBT\Participant\Section;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Section finished scoring.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class SectionScored implements ShouldBroadcast
{
    public Section $section;

    public function __construct(Section $section)
    {
        $this->section = $section;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('exam.'.$this->section->participant->exam_id);
    }
}
