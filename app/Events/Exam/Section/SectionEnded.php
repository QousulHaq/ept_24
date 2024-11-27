<?php

namespace App\Events\Exam\Section;

use Illuminate\Queue\SerializesModels;
use App\Entities\CBT\Participant\Section;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class SectionEnded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \App\Entities\CBT\Participant\Section
     */
    public Section $section;

    /**
     * Create a new event instance.
     *
     * @param \App\Entities\CBT\Participant\Section $section
     */
    public function __construct(Section $section)
    {
        $this->section = $section;
    }
}
