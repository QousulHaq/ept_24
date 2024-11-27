<?php

namespace App\Jobs\Package\Item;

use Jalameta\Support\Bus\BaseJob;
use App\Entities\Question\Package;
use App\Entities\Question\Package\Item;
use Illuminate\Contracts\Support\Responsable;
use App\Events\Package\PackageCreatedOrUpdated;

class DeleteExistingItem extends BaseJob implements Responsable
{
    /**
     * @var \App\Entities\Question\Package\Item
     */
    private Item $item;

    public function __construct(Item $item, ?Package $package = null, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->item = $item;

        if (! empty($package)) {
            $this->onSuccess(fn () => $package->touch());
            $this->onSuccess(fn () => event(new PackageCreatedOrUpdated($package)));
        }
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function run(): bool
    {
        return $this->item->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        return [
            'status' => $this->status,
            'data' => $this->item,
        ];
    }
}
