<?php

namespace App\Jobs\Package\Item;

use Illuminate\Http\Response;
use App\Entities\Classification;
use Jalameta\Support\Bus\BaseJob;
use App\Entities\Question\Package;
use App\Entities\Question\Package\Item;
use Illuminate\Contracts\Support\Responsable;
use App\Events\Package\PackageCreatedOrUpdated;
use App\Events\Package\Item\ItemCreatedOrUpdated;
use App\Http\Requests\Package\Item\StoreItemRequest;

class CreateNewItem extends BaseJob implements Responsable
{
    /**
     * @var array
     */
    private array $attributes;

    /**
     * @var \App\Entities\Question\Package\Item|null
     */
    private ?Item $item;

    /**
     * CreateNewItem constructor.
     *
     * @param \App\Http\Requests\Package\Item\StoreItemRequest $request
     * @param \App\Entities\Question\Package|null $package
     * @param array $inputs
     */
    public function __construct(StoreItemRequest $request, ?Package $package, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->attributes = $request->validated();

        if (! empty($package)) {
            $this->onSuccess(fn () => $this->item->packages()->attach($package->getAttribute('id')));
            $this->onSuccess(fn () => $package->touch());
            $this->onSuccess(fn () => event(new PackageCreatedOrUpdated($package)));
        }

        $this->onSuccess(fn () => event(new ItemCreatedOrUpdated($this->item)));
    }

    /**
     * {@inheritdoc}
     */
    public function run(): bool
    {
        $this->item = $this->createItem($this->attributes);

        return $this->item instanceof Item;
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        return new Response([
            'status' => $this->status,
            'message' => 'Item has been created...',
            'data' => $this->item,
        ]);
    }

    private function createItem(array $fields, ?Item $parent = null): ?Item
    {
        $item = new Item();
        $item->fill($fields);
        $item->setAttribute('item_count', count($fields['children'] ?? []) ?: 1);
        if ($parent !== null) {
            $item->parent()->associate($parent);
        }

        if ($item->save()) {
            if (count($fields['children'] ?? []) > 0) {
                foreach ($fields['children'] as $child) {
                    $this->createItem($child, $item);
                }
            }

            if (! empty($fields['answers'])) {
                $item->answers()->createMany($fields['answers']);
            }
        }

        if (isset($fields['attachment'])) {
            $item->attachments()->attach($fields['attachment']);
        }

        if (isset($fields['category'])) {
            $item->classifications()->attach(Classification::hashToId($fields['category']));
        }

        return $item;
    }
}
