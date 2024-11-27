<?php

namespace App\Http\Controllers\Api\BackOffice\Package;

use App\Entities\Question\Package;
use App\Http\Controllers\Controller;
use App\Jobs\Package\Item\CreateNewItem;
use App\Jobs\Package\Item\DeleteExistingItem;
use App\Jobs\Package\Item\UpdateExistingItem;
use App\Http\Requests\Package\Item\StoreItemRequest;
use App\Http\Requests\Package\Item\UpdateItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * @param \App\Http\Requests\Package\Item\StoreItemRequest $request
     * @param \App\Entities\Question\Package $package
     * @return \App\Jobs\Package\Item\CreateNewItem
     */
    public function store(StoreItemRequest $request, Package $package): CreateNewItem
    {
        $job = new CreateNewItem($request, $package);
        $this->dispatchNow($job);

        return $job;
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function show(Package $package, Package\Item $item): Package\Item
    {
        $item->answers->each(fn (Package\Item\Answer $answer) => $answer->makeVisible('correct_answer'));
        $item->children->each(function (Package\Item $item) {
            $item->answers->each(fn (Package\Item\Answer $answer) => $answer->makeVisible('correct_answer'));
        });

        return $item;
    }

    public function update(UpdateItemRequest $request, Package $package, Package\Item $item): UpdateExistingItem
    {
        $job = new UpdateExistingItem($request, $item, $package);
        $this->dispatchNow($job);

        return $job;
    }

    /**s
     * @param \App\Entities\Question\Package $package
     * @param \App\Entities\Question\Package\Item $item
     * @return mixed
     */
    public function destroy(Package $package, Package\Item $item): mixed
    {
//        $job = new DeleteExistingItem($item, $package);
//        $this->dispatchNow($job);

        $package->items()->detach($item);

        return $package;
    }
}
