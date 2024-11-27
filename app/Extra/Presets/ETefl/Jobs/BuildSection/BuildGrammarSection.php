<?php

namespace App\Extra\Presets\ETefl\Jobs\BuildSection;

use Illuminate\Support\Arr;
use App\Entities\CBT\Participant;
use App\Entities\Question\Package;
use App\Jobs\CBT\Section\Item\CreateNewItem;
use App\Extra\Repositories\Item\TypeMultiChoiceSingleRepository;

class BuildGrammarSection extends BaseBuildSection
{
    protected function prepare(): void
    {
        if (count($this->builders) !== 0) {
            return;
        }

        $lastPackage = null;
        $this->registerBuilder(function (TypeMultiChoiceSingleRepository $repository) use (&$lastPackage) {
            // render introduction
            if ($lastPackage !== $repository->package->getAttribute('title')) {
                $repository->package->introductions->each(function (Package\Item $item) use ($repository) {
                    (new CreateNewItem(
                        $this->section,
                        $item,
                        [
                            'order' => ($repository->package->introductions->count() * -1) + $repository->index,
                            'label' => 'INTRODUCTION '.strtoupper($repository->package->title),
                            'config' => Arr::get($repository->config, 'config'),
                            'remaining_time' => Arr::get($repository->config, 'intro.duration', 0),
                            'tags' => ['intro'],
                            'type' => Arr::get($repository->config, 'intro.type', Package\Item::TYPE_MULTI_CHOICE_SINGLE),
                            'is_encrypted' => $repository->package->is_encrypted,
                            'encryption_id' => $repository->package->distribution_options['package_id'] ?? null,
                        ]
                    ))->handle();
                });
            }
            $lastPackage = $repository->package->getAttribute('title');

            (new CreateNewItem($this->section, $repository->item, [
                'label' => $repository->index,
                'order' => $repository->index,
                'config' => Arr::get($repository->config, 'config'),
                'is_encrypted' => $repository->package->is_encrypted,
                'encryption_id' => $repository->package->distribution_options['package_id'] ?? null,
            ]))->handle();
        });
    }
}
