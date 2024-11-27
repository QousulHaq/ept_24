<?php

namespace App\Jobs\CBT\Section\Item;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use App\Entities\CBT\Participant\Section;
use App\Entities\CBT\Participant\Section\Item;
use App\Entities\Question\Package\Item as ItemReference;

class CreateNewItem implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Entities\CBT\Participant\Section\Item
     */
    public Item $item;

    /**
     * @var array
     */
    private array $attributes;

    public function __construct(
        public Section $section,
        protected ?ItemReference $itemReference = null,
        array $inputs = [])
    {
        // keep the input pure from the hand, please don't use inputs attribute,
        // because it has contains request attributes
        $this->attributes = $inputs;

        $this->item = new Item();
    }

    public function handle(): void
    {
        if ($this->run()) {
            $this->cloneAnswers();
            $this->cloneAttachments();
        }
    }

    public function run(): bool
    {
        $this->item->setAttribute('type', $this->attributes['type'] ?? ItemReference::TYPE_MULTI_CHOICE_SINGLE);
        if ($this->itemReference !== null) {
            $this->item->fill([
                'content' => $this->itemReference->getAttribute('content'),
                'remaining_time' => $this->itemReference->getAttribute('duration'),
            ]);
            $this->item->reference()->associate($this->itemReference);
        }

        $this->item->fill(Arr::only($this->attributes, [
            'config', 'type', 'label', 'content',
            'sub_content', 'remaining_time', 'order', 'tags',
            'is_encrypted', 'encryption_id',
        ]));
        $this->item->section()->associate($this->section);

        return $this->item->save();
    }

    private function cloneAnswers(): void
    {
        $answers = collect($this->inputs['answers'] ?? []);

        if ($this->itemReference->answers->count() > 0) {
            $answers = $this->itemReference->getAttribute('answer_order_random')
                ? $answers->merge($this->itemReference->answers->map->only(['order', 'correct_answer', 'content']))->shuffle()
                : $answers->merge($this->itemReference->answers->map->only(['order', 'correct_answer', 'content']));
        }

        // reordering answers and save them
        $order = 0;
        $this->item->answers()->createMany($answers->map(function (array $answer) use (&$order) {
            $answer['order'] = $order;
            $order++;

            $answer['is_encrypted'] = $this->attributes['is_encrypted'] ?? false;
            $answer['encryption_id'] = $this->attributes['encryption_id'] ?? null;

            return $answer;
        })->toArray());
    }

    private function cloneAttachments(): void
    {
        if ($this->itemReference->getAttribute('attachment') !== null || isset($this->attributes['attachments'])) {
            $this->item->attachments()->attach($this->itemReference->getAttribute('attachment') ?? $this->attributes['attachments']);
        }
    }
}
