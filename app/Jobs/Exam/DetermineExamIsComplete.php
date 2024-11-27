<?php

namespace App\Jobs\Exam;

use App\Entities\CBT\Exam;
use App\Entities\CBT\Participant;
use App\Events\Exam\ExamEnded;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetermineExamIsComplete implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Entities\CBT\Exam
     */
    private Exam $exam;

    /**
     * Create a new job instance.
     *
     * @param \App\Entities\CBT\Exam $exam
     */
    public function __construct(Exam $exam)
    {
        $this->exam = $exam;

        $this->onQueue('low');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (empty($exam->ended_at) && Participant::query()
                ->where('exam_id', $this->exam->id)
                ->where('status', Participant::STATUS_ACTIVE)
                ->doesntExist()) {
            event(new ExamEnded($this->exam));
        }
    }
}
