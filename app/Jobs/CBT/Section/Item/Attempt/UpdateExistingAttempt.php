<?php

namespace App\Jobs\CBT\Section\Item\Attempt;

use Illuminate\Http\Response;
use App\Entities\CBT\Participant\Section;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Validation\ValidationException;
use App\Notifications\CBT\Section\Item\AttemptSaved;

class UpdateExistingAttempt implements Responsable
{
    /**
     * UpdateExistingAttempt constructor.
     * @param \App\Entities\CBT\Participant\Section $section
     * @param \App\Entities\CBT\Participant\Section\Item $item
     * @param \App\Entities\CBT\Participant\Section\Item\Attempt $attempt
     * @param array $inputs
     * @throws \Throwable
     */
    public function __construct(
        private Section $section,
        private Section\Item $item,
        private Section\Item\Attempt $attempt,
        private array $inputs = [])
    {
        throw_if(!$section->item_duration && $section->getRemainingTime() <= 0,
            ValidationException::withMessages([
                'item' => 'remaining item is null'
            ])
        );

        throw_if($item->attempts()->where('id', $attempt->getAttribute('id'))->count() === 0,
            ValidationException::withMessages(['attempt doesn\'t match with item']));

        $attempt->touches('item');
    }

    public function handle(): void
    {
        $this->attempt->item()->associate($this->item);
        $this->attempt->setAttribute('attempt_number', $this->section->getAttribute('attempts'));

        $answer = $this->resolveAnswer();

        if ($answer) {
            $this->attempt->setAttribute('answer', $answer->getAttribute('content'));
        }

        if (array_key_exists('answer', $this->inputs)) {
            $this->attempt->setAttribute('answer', $this->inputs['answer']);
        }

        $this->attempt->save();

        $this->section->update(['last_attempted_at' => now()]);

        if (auth()->check()) {
            /**
             * @var $user \App\Entities\Account\User
             */
            $user = auth()->user();

            $user->notify(new AttemptSaved($this->attempt));
        }
    }

    public function toResponse($request)
    {
        return new Response([
            'status' => 'success',
            'message' => 'Attempt updated!',
            'data' => $this->attempt->fresh(),
        ], $this->attempt->exists ? 200 : 500);
    }

    private function resolveAnswer(): Section\Item\Answer|null
    {
        if (! array_key_exists('item_answer_id', $this->inputs)) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->item->answers()->findOrFail($this->inputs['item_answer_id']);
    }
}
