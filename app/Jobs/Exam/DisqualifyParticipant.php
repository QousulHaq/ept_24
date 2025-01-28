<?php

namespace App\Jobs\Exam;

use App\Entities\CBT\Exam;
use App\Entities\Account\User;
use App\Entities\CBT\Participant;
use Jalameta\Support\Bus\BaseJob;
use App\Events\Exam\Participant\ParticipantDisqualified;

class DisqualifyParticipant extends BaseJob
{
    /**
     * @var Participant
     */
    protected Participant $participant;

    /**
     * DisqualifiedParticipant constructor.
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
            if ($this->participant->status === Participant::STATUS_BANNED) {
                event(new ParticipantDisqualified($this->participant, $exam));
            }
        });

        // tanyain ini ke mas hadjir
        // $this->onSuccess(fn () => dispatch(new DetermineExamIsComplete($exam))->delay(now()->addMinutes(3)));
    }

    /**
     * {@inheritdoc}
     */
    public function run(): bool
    {
        if (in_array($this->participant->getAttribute('status'), [
                Participant::STATUS_NOT_READY,
                Participant::STATUS_READY,
                Participant::STATUS_ACTIVE
            ], true) || config('app.debug')) {
            $this->participant->setAttribute('status', Participant::STATUS_BANNED);

            return $this->participant->save();
        }

        return false;
    }
}
