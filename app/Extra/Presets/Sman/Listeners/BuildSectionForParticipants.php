<?php

namespace App\Extra\Presets\Sman\Listeners;

use App\Entities\CBT\Participant;
use App\Events\Exam\Participant\ParticipantReady;
use App\Extra\Presets\Sman\Jobs\BuildAllSection;
use App\Extra\Presets\Sman;
use App\Entities\Account\User;
use App\Events\Exam\ExamCreationFailed;
use Illuminate\Bus\Batch;
use App\Events\Exam\ExamCreatedOrUpdated;
use Illuminate\Support\Facades\Bus;

class BuildSectionForParticipants
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'high';

    /**
     * Handle the event.
     *
     * @param \App\Events\Exam\ExamCreatedOrUpdated $event
     * @return void
     * @throws \Throwable
     */
    public function handle(ExamCreatedOrUpdated $event)
    {
        // filter anything that should be not generate sections
        if (! $event->exam->package->preset instanceof Sman
            && $event->action === ExamCreatedOrUpdated::OTHER) {
            return;
        }

        Bus::batch(
            $event->exam->participants
                ->filter(fn (User $user) => !($event->action === ExamCreatedOrUpdated::UPDATED) || $user->detail->status !== Participant::STATUS_ACTIVE)
                ->map(fn (User $user) => new BuildAllSection($event->exam, $user))
                ->toArray()
        )
            ->then(function (Batch $batch) use ($event) {
                $event->exam->participants
                    ->filter(fn (User $user) => !($event->action === ExamCreatedOrUpdated::UPDATED) || $user->detail->status !== Participant::STATUS_ACTIVE)
                    ->each(fn(User $user) => event(new ParticipantReady($user->detail, $event->exam)));
            })
            ->catch(fn (Batch $batch, \Throwable $throwable) => event(new ExamCreationFailed($event->exam, $event->actor, $throwable)))
            ->onQueue('high')
            ->name('Generate sections for participants')
            ->dispatch();
    }
}
