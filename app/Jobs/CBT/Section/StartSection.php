<?php

namespace App\Jobs\CBT\Section;

use Illuminate\Http\Response;
use App\Entities\CBT\Participant\Section;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Jobs\CBT\Section\Item\Attempt\CreateNewAttempt;

class StartSection implements Responsable
{
    public const MESSAGE_SECTION_NOT_COMPLETE = 'the last section didn\'t complete.';

    /**
     * @var string|null
     */
    private ?string $message;

    public function __construct(
        private Section $section,
    ) {
        $this->section->load(['items.answers', 'items.attempts']);
    }

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        if ($this->section->getAttribute('last_attempted_at') &&
            ! $this->section->getAttribute('ended_at')) {
            $this->message = self::MESSAGE_SECTION_NOT_COMPLETE;

            return;
        }

        $this->section->setAttribute('last_attempted_at', now());
        $this->section->setAttribute('ended_at', null);

        $this->section->save();

        if ($this->section->exists) {
            // resetting remaining time each item
            if ($this->section->getAttribute('item_duration')) {
                $this->section->items->each(fn (Section\Item $item) => $item->setAttribute('remaining_time', $item->reference->getAttribute('duration')));
            }

            $this->section->incrementAttempts();

            $this->section->items->each(fn (Section\Item $item) => dispatch_sync(new CreateNewAttempt($this->section, $item)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        return new Response([
            'status' => 'success',
            'message' => $this->message ?? '',
            'data' => $this->section->fresh([
                'items.attachments',
                'items.answers',
                'items.attempts' => fn (HasMany $builder) => $builder->where('attempt_number', $this->section->getAttribute('attempts')),
            ]),
        ], 200);
    }
}
