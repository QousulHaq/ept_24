<?php

namespace App\Listeners\Exam;

use App\Jobs\Exam\DeleteExistingExam;
use App\Events\Exam\ExamCreationFailed;

class DeleteExamOnCreationFailed
{
    /**
     * Handle the event.
     *
     * @param \App\Events\Exam\ExamCreationFailed $event
     * @return void
     */
    public function handle(ExamCreationFailed $event)
    {
        dispatch(new DeleteExistingExam($event->exam))->delay(now()->addMinutes(3));
    }
}
