<?php

namespace App\Listeners\Exam\Participant;

use App\Entities\CBT\Participant;
use App\Entities\CBT\Participant\Section;
use App\Events\Exam\Participant\ParticipantFinish;
use App\Events\Exam\Section\SectionEnded;
use App\Jobs\Exam\DetermineExamIsComplete;
use Illuminate\Contracts\Queue\ShouldQueue;

class DetermineParticipantIsComplete implements ShouldQueue
{

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public string $queue = 'high';

    /**
     * Handle the event.
     *
     * @param \App\Events\Exam\Section\SectionEnded $event
     * @return void
     */
    public function handle(SectionEnded $event)
    {
        if (Section::query()
            ->where('participant_id', $event->section->participant_id)
            ->whereNull('ended_at')
            ->doesntExist()) {
            $event->section->participant->update(['status' => Participant::STATUS_FINISHED]);
            event(new ParticipantFinish($event->section->participant, $event->section->participant->exam));

            dispatch(new DetermineExamIsComplete($event->section->participant->exam));
        }
    }
}
