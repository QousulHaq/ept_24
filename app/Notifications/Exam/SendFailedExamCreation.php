<?php

namespace App\Notifications\Exam;

use App\Entities\CBT\Exam;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SendFailedExamCreation extends Notification
{
    use Queueable;

    /**
     * @var \App\Entities\CBT\Exam
     */
    public Exam $exam;
    private \Exception $exception;

    /**
     * Create a new notification instance.
     *
     * @param \App\Entities\CBT\Exam $exam
     * @param \Exception $exception
     */
    public function __construct(Exam $exam, \Exception $exception)
    {
        $this->exam = $exam;
        $this->exception = $exception;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toDatabase()
    {
        return [
            'exam_id' => $this->exam->id,
            'level' => 'danger',
            'message' => 'Failed to generate exam questions for "'.$this->exam->name.
                '". Exam : '.$this->exam->name.' will be deleted automatically in 3 minutes.',
            'detail' => $this->exception->getMessage(),
        ];
    }

    public function toArray()
    {
        return [
            'level' => 'danger',
            'message' => 'Failed to generate exam questions for "'.$this->exam->name.
                '". Exam : '.$this->exam->name.' will be deleted automatically in 3 minutes.',
            'detail' => $this->exception->getMessage(),
        ];
    }
}
