<?php

namespace App\Events\Exam;

use App\Entities\CBT\Exam;
use App\Entities\Account\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ExamCreatedOrUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public const OTHER = 'other';
    public const CREATED = 'created';
    public const UPDATED = 'updated';

    /**
     * @var \App\Entities\CBT\Exam
     */
    public Exam $exam;

    /**
     * @var \App\Entities\Account\User
     */
    public User $actor;

    public string $action;

    /**
     * Create a new event instance.
     *
     * @param \App\Entities\CBT\Exam $exam
     * @param \App\Entities\Account\User $actor
     * @param string $action
     */
    public function __construct(Exam $exam, User $actor, string $action = self::OTHER)
    {
        $this->exam = $exam;
        $this->actor = $actor;
        $this->action = $action;
    }
}
