<?php

namespace App\Events\Exam;

use App\Entities\CBT\Exam;
use App\Entities\Account\User;

class ExamCreationFailed extends ExamCreatedOrUpdated
{
    public \Throwable $exception;

    public function __construct(Exam $exam, User $actor, \Throwable $exception)
    {
        parent::__construct($exam, $actor);

        $this->exception = $exception;
    }
}
