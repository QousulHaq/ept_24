<?php

namespace App\Notifications\CBT\Section\Item;

use App\Notifications\CBT\BaseNotification;
use App\Entities\CBT\Participant\Section\Item\Attempt;

class AttemptSaved extends BaseNotification
{
    /**
     * @var \App\Entities\CBT\Participant\Section\Item\Attempt|null
     */
    private ?Attempt $attempt;

    public function __construct(?Attempt $attempt = null, $duration = 1.5)
    {
        $this->attempt = $attempt;
        $this->duration = $duration;

        $this->payload = [
            'content' => 'Answer '.(! $this->attempt ? '' : $this->attempt->item->label).' is saved 👍!',
            'duration' => $this->duration,
        ];
    }

    public function via()
    {
        if ($this->attempt !== null && ! is_numeric($this->attempt->item->getAttribute('label'))) {
            return [];
        }

        return ['broadcast'];
    }
}
