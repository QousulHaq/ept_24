<?php

namespace App\Jobs\Package\Item;

use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use App\Entities\Classification;
use Jalameta\Support\Bus\BaseJob;
use App\Entities\Question\Package;
use Illuminate\Contracts\Support\Responsable;
use App\Extra\Eloquent\Scopes\RootEntityScope;
use App\Events\Package\PackageCreatedOrUpdated;
use App\Events\Package\Item\ItemCreatedOrUpdated;
use App\Http\Requests\Package\Item\UpdateItemRequest;

class UpdateExistingItem extends BaseJob implements Responsable
{
    /**
     * @var \App\Entities\Question\Package\Item
     */
    private Package\Item $item;

    /**
     * @var array
     */
    private array $attributes;

    public function __construct(UpdateItemRequest $request, Package\Item $item, ?Package $package = null, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->item = $item;

        $this->attributes = $request->validated();
        $this->attributes['id'] = $item->getAttribute('id');

        $this->onSuccess(fn () => event(new ItemCreatedOrUpdated($this->item)));
        if (! empty($package)) {
            $this->onSuccess(fn () => $package->touch());
            $this->onSuccess(fn () => event(new PackageCreatedOrUpdated($package)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run(): bool
    {
        $this->updateItem($this->attributes);

        return $this->item instanceof Package\Item;
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        return new Response([
            'status' => $this->status,
            'message' => 'Item has been updated!',
            'data' => $this->item,
        ]);
    }

    private function updateItem(array $fields, ?Package\Item $parent = null)
    {
        if (isset($fields['id'])) {
            $item = Package\Item::query()->withoutGlobalScope(RootEntityScope::class)->find($fields['id']);
        } else {
            $item = new Package\Item();
            if ($parent !== null) {
                $item->parent()->associate($parent);
            }
        }

        $item->setAttribute('item_count', count($fields['children'] ?? []) ?: 1);
        $item->fill($fields);

        if ($item->save()) {
            // children update section
            $children = collect($fields['children'] ?? []);
            $item->children()->whereNotIn('id', $children->filter(fn ($child) => ! empty($child['id']))->map->id)->get()->each->delete();
            $children->each(fn (array $child) => $this->updateItem($child, $item));

            // answers update section
            $answers = collect($fields['answers'] ?? []);
            $item->answers()->whereNotIn('id', $answers->filter(fn ($answer) => ! empty($answer['id']))->map->id)->get()->each->delete();
            $answers->each(function (array $field) use ($item) {
                $answer = isset($field['id'])
                    ? Package\Item\Answer::query()->findOrFail($field['id'])
                    : new Package\Item\Answer();

                $answer->fill(Arr::only($field, ['order', 'content', 'correct_answer']));
                $answer->item()->associate($item);

                $answer->save();
            });
        }

        if (isset($fields['attachment'])) {
            $item->attachments()->sync([$fields['attachment']]);
        }

        if (isset($fields['category'])) {
            $item->classifications()->sync([Classification::hashToId($fields['category'])]);
        }
    }
}
