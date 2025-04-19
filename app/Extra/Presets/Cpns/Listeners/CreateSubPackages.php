<?php

namespace App\Extra\Presets\Cpns\Listeners;

use Illuminate\Support\Arr;
use App\Extra\Presets\Cpns;
use App\Entities\Classification;
use App\Entities\Question\Package;
use App\Entities\Question\Package\Item;
use App\Events\Package\PackageCreatedOrUpdated;

class CreateSubPackages
{
    /**
     * Handle the event.
     *
     * @param \App\Events\Package\PackageCreatedOrUpdated $event
     * @return void
     */
    public function handle(PackageCreatedOrUpdated $event)
    {
        if (! $event->package->preset instanceof Cpns) {
            return;
        }

        if ($event->package->children()->count() === 0 && $event->package->getRawOriginal('parent_id') === null) {
            $this->createSubpackage($event->package, Cpns::$subPresetTree);
        }
    }

    private function createSubpackage(Package $parent, array $presets, $depth = 1): void
    {
        foreach ($presets as $preset) {
            /**
             * @var $child Package
             */
            $child = $parent->children()->create(array_merge(
                Arr::only($preset, [
                    'title', 'code', 'description', 'level', 'duration', 'max_score',
                    'random_item', 'item_duration', 'config',
                ]),
                ['depth' => $depth]
            ));

            if (! empty($preset['categories'])) {
                foreach ($preset['categories'] as $category) {
                    $category = Classification::query()->firstOrCreate(['name' => $category]);
                    $child->classifications()->attach($category->getAttribute('id'));
                }
            }

            if (! empty($preset['intro_code'])) {
                $intro = Item::query()->where('code', $preset['intro_code'])
                    ->first();

                if (! empty($intro)) {
                    $child->introductions()->attach($intro, ['type' => Package::PACKAGE_ITEM_TYPE_INTRO]);
                }
            }

            if (! empty($preset['sub-preset'])) {
                $this->createSubpackage($child, $preset['sub-preset'], ++$depth);
            }
        }
    }
}
