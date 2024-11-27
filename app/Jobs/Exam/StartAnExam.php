<?php

namespace App\Jobs\Exam;

use App\Entities\CBT\Exam;
use App\Entities\CBT\Participant;
use App\Events\Exam\ExamStarted;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Queue\ShouldQueue;

class StartAnExam implements ShouldQueue
{
    /**
     * @var \App\Entities\CBT\Exam
     */
    private Exam $exam;

    /**
     * StartAnExam constructor.
     *
     * @param \App\Entities\CBT\Exam $exam
     * @param array $inputs
     * @throws \Throwable
     */
    public function __construct(Exam $exam, array $inputs = [])
    {
        $this->exam = $exam;

        throw_if($exam->participants()->where('status', Participant::STATUS_NOT_READY)->exists(), ValidationException::withMessages([
            'exam_status' => 'Wait system to generate sections !'
        ]));
    }

    public function handle(): void
    {
        $this->exam->update(['started_at' => now()]);

        broadcast(new ExamStarted($this->exam));
    }
}
