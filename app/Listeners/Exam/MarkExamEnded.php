<?php

namespace App\Listeners\Exam;

use App\Events\Exam\ExamEnded;
use Illuminate\Contracts\Queue\ShouldQueue;

class MarkExamEnded implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param \App\Events\Exam\ExamEnded $event
     * @return void
     */
    public function handle(ExamEnded $event)
    {
        $event->exam->update(['ended_at' => now()]);
    }
}
