<?php

namespace App\Extra\Presets\ETefl\Jobs\Package;

use Illuminate\Support\Arr;
use App\Extra\Presets\ETefl;
use Illuminate\Support\Fluent;
use Jalameta\Support\Bus\BaseJob;
use App\Entities\Question\Package;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Queue\ShouldQueue;

class BuildNoteForPackage extends BaseJob implements ShouldQueue
{
    public $queue = 'low';

    /**
     * @var \App\Entities\Question\Package
     */
    private Package $package;

    private Collection $errors;

    /**
     * BuildNoteForPackage constructor.
     * @param \App\Entities\Question\Package $package
     * @param array $inputs
     */
    public function __construct(Package $package, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->package = $package;
        $this->errors = new Collection();
    }

    public function run(): bool
    {
        if (! $this->package->preset instanceof ETefl || ! is_null($this->package->parent_id)) {
            return false;
        }

        $this->checkSubpackage($this->package->children->firstWhere('config.title', ETefl::SECTION_LISTENING));
        $this->checkSubpackage($this->package->children->firstWhere('config.title', ETefl::SECTION_GRAMMAR));
        $this->checkSubpackage($this->package->children->firstWhere('config.title', ETefl::SECTION_READING));

        $this->package->update([
            'note' => $this->errors,
        ]);

        return true;
    }


    private function checkSubpackage(?Package $package)
    {
        if (! $package) {
            return false;
        }

        $packages = empty($package->config['sub-preset'])
            ? collect([$package])
            : collect($package->config['sub-preset'])
                ->map(fn (array $presetConfig) => $package->children
                    ->first(fn (Package $subpackage) => $subpackage->getRawOriginal('config') === $presetConfig['config']));

        $packages->each(function (Package $package) {
            $presetConfig = $package->config;
            $categories = collect(Arr::get($presetConfig, 'categories', []));
            $expectedItemTotal = Arr::get($presetConfig, 'item_total', $package->items->count());

            $categoriesTotal = $categories->count();
            if ($categoriesTotal > 0) {
                $takeEachCategories = floor($expectedItemTotal / $categoriesTotal);
                $categories->each(function (string $categoryName) use ($package, $takeEachCategories) {
                    $resultAmount = $package->items->where('category_name', $categoryName)->count();
                    $this->addErrorIf(
                        $resultAmount < $takeEachCategories,
                        $package,
                        'expected for category "'.$categoryName.'" is '.$takeEachCategories.' questions got '.$resultAmount);
                });
            }

            $this->addErrorIf(
                $package->items->count() < $expectedItemTotal,
                $package,
                'expected have '.$expectedItemTotal.' questions got '.$package->items->count());

            $package->items->each(function (Package\Item $item) use ($presetConfig, $package) {
                $requireSubItem = Arr::get($presetConfig, 'item.item_count', 0);
                $itemChildrenAmount = $item->children()->count();
                $this->addErrorIf(
                    $itemChildrenAmount < $requireSubItem,
                    $package,
                    'expected sub question of '.$item->code.' is '.$requireSubItem.' questions got '.$itemChildrenAmount);
            });
        });

        return true;
    }

    private function addErrorIf(bool $condition, Package $package, string $message)
    {
        if ($condition) {
            $this->addError($package, $message);
        }
    }

    private function addError(Package $package, string $message)
    {
        /* @var $error Fluent|null */
        $error = $this->errors->firstWhere('package', $package->title);

        if (! $error) {
            return $this->errors->push(new Fluent([
                'package' => $package->title,
                'message' => collect([$message]),
            ]));
        }

        return $error->get('message')->push($message);
    }
}
