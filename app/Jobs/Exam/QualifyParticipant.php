<?php

namespace App\Jobs\Exam;

use App\Entities\CBT\Exam;
use App\Entities\Account\User;
use App\Entities\CBT\Participant;
use Jalameta\Support\Bus\BaseJob;
use App\Events\Exam\Participant\ParticipantQualified;

class QualifyParticipant extends BaseJob
{
    /**
     * @var Participant
     */
    protected Participant $participant;

    /**
     * Create a new job instance.
     *
     * @param Exam $exam
     * @param User $user
     * @param array $inputs
     */
    public function __construct(Exam $exam, User $user, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->participant = $exam->participants()->wherePivot('user_id', $user->id)->first()->detail;
        $this->onSuccess(function () use ($exam) {
            if ($this->participant->status !== Participant::STATUS_BANNED) {
                event(new ParticipantQualified($this->participant, $exam));
            }
        });
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function run(): bool
    {
        if ($this->participant->getAttribute('status') === Participant::STATUS_BANNED) {
            if ($this->participant->sections->first()->last_attempted_at !== null) {
                $this->participant->setAttribute('status', Participant::STATUS_ACTIVE);
            } else {
                $this->participant->setAttribute('status', Participant::STATUS_READY);
            }

            return $this->participant->save();
        }

        return false;
    }
}
