<?php

namespace App\Jobs\CBT\Section\Item\Attempt;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Entities\CBT\Participant\Section;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateNewAttempt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * CreateNewAttempt constructor.
     *
     * @param \App\Entities\CBT\Participant\Section $section
     * @param \App\Entities\CBT\Participant\Section\Item $item
     * @param array $inputs
     */
    public function __construct(
        private Section $section,
        private Section\Item $item,
        private array $inputs = [])
    {}

    public function handle(): void
    {
        $attempt = new Section\Item\Attempt();

        $attempt->item()->associate($this->item);
        $attempt->setAttribute('attempt_number', $this->section->getAttribute('attempts'));

        $answer = $this->resolveAnswer();
        if ($answer) {
            $attempt->setAttribute('answer', $answer->getAttribute('content'));
        }

        if (array_key_exists('answer', $this->inputs)) {
            $attempt->setAttribute('answer', $this->inputs['answer']);
        }

        $attempt->save();
    }

    protected function resolveAnswer(): ?Section\Item\Answer
    {
        if (! array_key_exists('item_answer_id', $this->inputs)) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->item->answers()->findOrFail($this->inputs['item_answer_id']);
    }
}
