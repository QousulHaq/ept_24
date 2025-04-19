<?php

namespace App\Extra\Presets\Sman\Jobs\BuildSection;

use Illuminate\Support\Arr;
use App\Entities\CBT\Participant;
use App\Entities\Question\Package;
use App\Jobs\CBT\Section\Item\CreateNewItem;
use App\Extra\Repositories\Item\TypeBundleRepository;
use App\Entities\Question\Package\Item as ItemReference;
use Illuminate\Support\Collection;

class BuildPenalaranSection extends BaseBuildSection
{
    protected function prepare(): void
    {
        if (count($this->builders) !== 0) {
            return;
        }

        $this->registerBuilder(function (TypeBundleRepository $repository) {
            if ($repository->index === 1) {
                $this->buildAnIntroduction($repository);
            }

            $repository->children->each(function (ItemReference $itemRefChild, $index) use ($repository) {
                (new CreateNewItem($this->section, $itemRefChild, [
                    'label' => $repository->index + $index,
                    'content' => $repository->parent->getAttribute('content'),
                    'sub_content' => $itemRefChild->getAttribute('content'),
                    'config' => Arr::get($repository->config, 'config'),
                    'order' => $repository->index + $index,
                    'is_encrypted' => $repository->package->is_encrypted,
                    'encryption_id' => $repository->package->distribution_options['package_id'] ?? null,
                ]))->handle();
            });
        });
    }

    private function buildAnIntroduction(TypeBundleRepository $repository): void
    {
        $repository->package->introductions->each(function (Package\Item $item) use ($repository) {
            (new CreateNewItem(
                $this->section,
                $item,
                [
                    'order' => ($repository->package->introductions->count() * -1) + $repository->index,
                    'label' => 'INTRODUCTION '.strtoupper($repository->package->title),
                    'config' => Arr::get($repository->config, 'config'),
                    'remaining_time' => Arr::get($repository->config, 'intro.duration', 0),
                    'type' => Arr::get($repository->config, 'intro.type', Package\Item::TYPE_MULTI_CHOICE_SINGLE),
                    'tags' => ['intro'],
                    'is_encrypted' => $repository->package->is_encrypted,
                    'encryption_id' => $repository->package->distribution_options['package_id'] ?? null,
                ]
            ))->handle();
        });
    }
}
