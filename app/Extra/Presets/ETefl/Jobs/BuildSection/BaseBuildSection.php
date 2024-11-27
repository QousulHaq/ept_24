<?php

namespace App\Extra\Presets\ETefl\Jobs\BuildSection;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use App\Entities\CBT\Participant;
use App\Entities\Question\Package;
use App\Extra\Repositories\Item\BaseTypeRepository;
use App\Extra\Repositories\Item\TypeBundleRepository;
use App\Exceptions\CBT\Generator\GenerateSectionException;
use App\Extra\Repositories\Item\TypeMultiChoiceSingleRepository;

abstract class BaseBuildSection implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Entities\CBT\Participant\Section
     */
    protected Participant\Section $section;

    /**
     * a list of builder that take responsibility to copy items.
     *
     * @var array
     */
    protected array $builders = [];

    public function __construct(protected Package $package,
                                protected Participant $participant)
    {}

    /**
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function handle(): void
    {
        $this->section = new Participant\Section();

        $this->section->fill([
            'item_duration' => $this->package->getAttribute('item_duration'),
            'config' => $this->package->getRawOriginal('config'),
            'remaining_time' => Arr::get($this->package->getAttribute('config'), 'duration', 0),
        ]);

        $this->section->participant()->associate($this->participant);

        $this->section->save();

        $this->prepare();

        $this->buildItemsFromPackage();
    }

    abstract protected function prepare(): void;

    public function registerBuilder(\Closure $closure): void
    {
        $this->builders[] = $closure;
    }

    /**
     * @param \App\Extra\Repositories\Item\BaseTypeRepository $baseTypeRepository
     * @return void
     * @throws \ReflectionException
     */
    public function resolveBuilder(BaseTypeRepository $baseTypeRepository): void
    {
        foreach ($this->builders as $builder) {
            $reflection = new \ReflectionFunction($builder);

            if ($reflection->getParameters()[0]->getType()?->getName() === get_class($baseTypeRepository)) {
                $builder($baseTypeRepository);
            }
        }
    }

    /**
     * @throws \ReflectionException|\Throwable
     */
    protected function buildItemsFromPackage(): void
    {
        // build collection of package that compatible with nested or not package
        $packages = collect();

        if (!empty($this->package->config['sub-preset'])) {
            $packages = collect($this->package->config['sub-preset'])->map(function (array $presetConfig) {
                return $this->package->children->first(function (Package $package) use ($presetConfig) {
                    return $package->getRawOriginal('config') === $presetConfig['config'];
                });
            });
        } else {
            $packages->push($this->package);
        }

        $indexItem = 1;
        $packages->each(function (Package $package) use (&$indexItem) {
            //// !! determine which items should be used and random it
            $presetConfig = $package->config;

            $categories = Arr::get($presetConfig, 'categories', []);

            $items = collect();

            $warnings = collect();

            $expectedItemTotal = Arr::get($presetConfig, 'item_total', $package->items->count());
            $categoriesTotal = count($categories);
            if ($categoriesTotal > 0) {
                $takeEachCategories = floor($expectedItemTotal / $categoriesTotal);
                $items = $items->merge(collect($categories)
                    ->map(function (string $categoryName) use ($package, $takeEachCategories, &$warnings) {
                        $result = $package->items->where('category_name', $categoryName)->take($takeEachCategories);

                        if ($result->count() < $takeEachCategories) {
                            $warnings->push("Items total of \"{$package->title}\" with category " .
                                "\"{$categoryName}\" doesn't fit [{$result->count()}/{$takeEachCategories}]");
                        }

                        return $result;
                    })->flatten());
                $residue = $expectedItemTotal % $categoriesTotal;
                if ($residue > 0) {
                    $items = $items->merge($package->items->whereNotIn('id', $items->map->id)->take($residue));
                }
            } else {
                $items = $items->merge($package->items->take($expectedItemTotal));
            }

            throw_if($items->count() < $expectedItemTotal, GenerateSectionException::class,
                get_class($this) . '@buildItemsFromPackage\\*\\SelectingItems',
                "Items total of \"{$package->title}\" doesn't fit " .
                "with selected items [{$items->count()}/{$expectedItemTotal}]!. " . $warnings->implode(''));

            $items = $package->getAttribute('random_item')
                ? $items->shuffle()
                : $items->sortBy('order');

            //// !! building repository to store important data that bridging into builder...
            $items->each(function (Package\Item $itemRef) use ($presetConfig, $package, &$indexItem) {
                $repo = null;

                switch ($itemRef->getAttribute('type')) {
                    case Package\Item::TYPE_BUNDLE:
                        $children = $itemRef->children;

                        $requireSubItem = Arr::get($presetConfig, 'item.item_count', 0);
                        throw_if($children->count() < $requireSubItem, GenerateSectionException::class,
                            get_class($this) . '@buildItemsFromPackage\\*\\items\\*\\' . Package\Item::TYPE_BUNDLE,
                            "item children of {$itemRef->getAttribute('code')} doesn't fit [{$children->count()}/{$requireSubItem}]");

                        $children = $children->take($requireSubItem);

                        // TODO: think about custom random item for children
                        $repo = new TypeBundleRepository(
                            $package,
                            $itemRef,
                            $package->getAttribute('random_item') ? $children->shuffle() : $children,
                            $presetConfig,
                            $indexItem);

                        $indexItem += ($requireSubItem - 1);
                        break;
                    case Package\Item::TYPE_MULTI_CHOICE_SINGLE:
                        $repo = new TypeMultiChoiceSingleRepository($package, $itemRef, $presetConfig, $indexItem);
                        break;
                }

                if ($repo !== null) {
                    $this->resolveBuilder($repo);
                }

                $indexItem++;
            });
        });
    }
}
