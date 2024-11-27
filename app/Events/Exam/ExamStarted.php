<?php

namespace App\Events\Exam;

use App\Entities\CBT\Exam;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ExamStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \App\Entities\CBT\Exam
     */
    public Exam $exam;

    /**
     * Create a new event instance.
     *
     * @param \App\Entities\CBT\Exam $exam
     */
    public function __construct(Exam $exam)
    {
        $this->exam = $exam;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('exam.'.$this->exam->id);
    }
}
