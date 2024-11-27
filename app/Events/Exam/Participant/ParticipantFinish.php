<?php

namespace App\Events\Exam\Participant;

use App\Entities\CBT\Exam;
use App\Entities\CBT\Participant;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ParticipantFinish implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \App\Entities\CBT\Participant
     */
    public Participant $participant;

    /**
     * @var \App\Entities\CBT\Exam
     */
    private Exam $exam;

    /**
     * Create a new event instance.
     *
     * @param \App\Entities\CBT\Participant $participant
     * @param \App\Entities\CBT\Exam|null $exam
     */
    public function __construct(Participant $participant, ?Exam $exam = null)
    {
        $this->participant = $participant;
        $this->exam = $exam ?? $participant->exam;
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
