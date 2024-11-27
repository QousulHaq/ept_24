<?php

namespace App\Listeners\Exam;

use App\Events\Exam\ExamCreationFailed;
use App\Notifications\Exam\SendFailedExamCreation;

class SendNotificationCreationFailed
{
    /**
     * Handle the event.
     *
     * @param \App\Events\Exam\ExamCreationFailed $event
     * @return void
     */
    public function handle(ExamCreationFailed $event)
    {
        $event->actor->notify(new SendFailedExamCreation($event->exam, $event->exception));
    }
}
