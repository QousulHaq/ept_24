<?php

namespace App\Listeners\Exam\Participant;

use App\Entities\CBT\Participant;
use App\Events\Exam\Participant\ParticipantReady;

class MarkParticipantAsReady
{
    /**
     * Handle the event.
     *
     * @param \App\Events\Exam\Participant\ParticipantReady $event
     * @return void
     */
    public function handle(ParticipantReady $event)
    {
        $event->participant->status = Participant::STATUS_READY;
        $event->participant->save();
    }
}
