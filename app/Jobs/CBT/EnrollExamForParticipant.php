<?php

namespace App\Jobs\CBT;

use Carbon\Carbon;
use App\Entities\CBT\Exam;
use Illuminate\Http\Response;
use App\Entities\CBT\Participant;
use Jalameta\Support\Bus\BaseJob;
use App\Extra\Repositories\TokenRepository;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Validation\ValidationException;

class EnrollExamForParticipant extends BaseJob implements Responsable
{
    private Participant $participant;

    private Exam $exam;

    /**
     * @var \Carbon\Carbon
     */
    private Carbon $now;
    private ?int $jwtExpiresAt;

    /**
     * EnrollExamForParticipant constructor.
     * @param \App\Entities\CBT\Exam $exam
     * @param array $inputs
     * @throws \Throwable
     */
    public function __construct(Exam $exam, array $inputs = [])
    {
        parent::__construct($inputs);

        $user = $exam->participants()->wherePivot('user_id', auth('api')->id())->first();

        $this->participant = $user->detail;
        $this->exam = $exam;
        $this->now = Carbon::now();
        $this->jwtExpiresAt = null;

        throw_if(is_null($user), ValidationException::withMessages(['exam' => 'this exam is not meant for you.']));
        throw_if($this->participant->getAttribute('status') === Participant::STATUS_BANNED,
            ValidationException::withMessages(['exam' => 'you has been banned for this exam.']));
        throw_if($this->participant->sections()->whereNull('ended_at')->doesntExist(),
            ValidationException::withMessages(['exam' => 'all section is done.']));
//        throw_if($this->participant->getAttribute('status') === Participant::STATUS_ACTIVE && ! config('app.debug'),
//            ValidationException::withMessages(['exam' => 'you are logged on in difference device']));
    }

    /**
     * {@inheritdoc}
     */
    public function run(): bool
    {
        if (!in_array($this->participant->getAttribute('status'), [Participant::STATUS_NOT_READY, Participant::STATUS_BANNED], true) || config('app.debug')) {
            $this->participant->setAttribute('status', Participant::STATUS_ACTIVE);

            return $this->participant->save();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        return new Response([
            'status' => $this->status,
            'data' => ($this->status === self::STATUS_SUCCESS) ? [
                'signature' => $this->getSignature(),
                'expires_in' => $this->getExpiresAt(),
            ] : [],
        ], ($this->status === self::STATUS_SUCCESS) ? 200 : 422);
    }

    private function getSignature(): string
    {
        return TokenRepository::generate($this->participant->getAttribute('id'), Carbon::createFromTimestamp($this->getExpiresAt())->toDateTimeImmutable())
            ->toString();
    }

    private function getExpiresAt(): int
    {
        if (is_null($this->jwtExpiresAt)) {
            $this->jwtExpiresAt = $this->now->addHours(5)->timestamp;
        }

        return $this->jwtExpiresAt;
    }
}
