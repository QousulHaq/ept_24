<?php

namespace App\Extra\Presets\ETefl\Jobs\BuildSection;

use Illuminate\Support\Arr;
use App\Entities\Media\Attachment;
use App\Entities\Question\Package;
use App\Jobs\CBT\Section\Item\CreateNewItem;
use App\Extra\Repositories\Item\TypeBundleRepository;
use App\Extra\Repositories\Item\TypeMultiChoiceSingleRepository;

class BuildListeningSection extends BaseBuildSection
{
    protected function prepare(): void
    {
        $additional = 0;
        // for type a that have type multi_choice_single
        $this->registerBuilder(function (TypeMultiChoiceSingleRepository $repository) use (&$additional) {
            // render introduction
            if ($repository->index === 1) {
                $repository->package->introductions->each(function (Package\Item $item) use ($repository, &$additional) {
                    (new CreateNewItem(
                        $this->section,
                        $item,
                        [
                            'order' => $repository->index + ($additional++),
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

            $createNewItem = new CreateNewItem($this->section, $repository->item, [
                'order' => $repository->index + $additional,
                'label' => $repository->index,
                'config' => Arr::get($repository->config, 'config'),
                'is_encrypted' => $repository->package->is_encrypted,
                'encryption_id' => $repository->package->distribution_options['package_id'] ?? null,
            ]);

            $createNewItem->handle();

            $this->registerAttachmentForNumbering($createNewItem);
        });

        /**
         * @var $before null|TypeBundleRepository
         */
        $before = null;

        // for type b and c that have type bundle
        $this->registerBuilder(function (TypeBundleRepository $repository) use (&$additional, &$before) {
            // render introduction
            if ($before === null || $before->package->title !== $repository->package->title) {
                $repository->package->introductions->each(function (Package\Item $item) use ($repository, &$additional) {
                    $label = 'INTRODUCTION '.strtoupper($repository->package->title);
                    (new CreateNewItem(
                        $this->section,
                        $item,
                        [
                            'order' => $repository->index + ($additional++),
                            'label' => $label,
                            'config' => Arr::get($repository->config, 'config'),
                            'remaining_time' => Arr::get($repository->config, 'intro.duration', 0),
                            'type' => Arr::get($repository->config, 'intro.type', Package\Item::TYPE_BUNDLE),
                            'tags' => ['intro'],
                            'is_encrypted' => $repository->package->is_encrypted,
                            'encryption_id' => $repository->package->distribution_options['package_id'] ?? null,
                        ]
                    ))->handle();

                    $i = 1;
                    $item->children->each(function (Package\Item $itemRefChild) use ($repository, &$additional, $label, &$i) {
                        (new CreateNewItem($this->section, $itemRefChild, [
                            'label' => substr($label, 0, 5).'#'.($i++),
                            'order' => $repository->index + ($additional++),
                            'config' => Arr::get($repository->config, 'config'),
                            'type' => Arr::get($repository->config, 'intro.type', Package\Item::TYPE_BUNDLE),
                            'tags' => ['intro'],
                            'is_encrypted' => $repository->package->is_encrypted,
                            'encryption_id' => $repository->package->distribution_options['package_id'] ?? null,
                        ]))->handle();
                    });
                });
            }

            $createNewItem = new CreateNewItem($this->section, $repository->parent, [
                'order' => $repository->index + ($additional++),
                'label' => '#',
                'config' => Arr::get($repository->config, 'config'),
                'remaining_time' => Arr::get($repository->config, 'item.duration', 0),
                'type' => Package\Item::TYPE_BUNDLE,
                'tags' => ['passage'],
                'is_encrypted' => $repository->package->is_encrypted,
                'encryption_id' => $repository->package->distribution_options['package_id'] ?? null,
            ]);

            $createNewItem->handle();

            $this->registerAttachmentForNumbering($createNewItem,
                'etefl_audio_passage_'.$repository->index .'_to_'. ($repository->index + $repository->children->count() - 1));

            $repository->children->each(function (Package\Item $itemRefChild) use ($repository, $additional) {
                $createNewItem = new CreateNewItem($this->section, $itemRefChild, [
                    'label' => $repository->index,
                    'order' => $repository->index + $additional,
                    'config' => Arr::get($repository->config, 'config'),
                    'type' => Package\Item::TYPE_BUNDLE,
                    'is_encrypted' => $repository->package->is_encrypted,
                    'encryption_id' => $repository->package->distribution_options['package_id'] ?? null,
                ]);

                $createNewItem->handle();

                $this->registerAttachmentForNumbering($createNewItem);

                $repository->index++;
            });

            $before = $repository;
        });
    }

    private function registerAttachmentForNumbering(CreateNewItem $createNewItem, $title = null): void
    {
        if (! $title) {
            $title = 'etefl_audio_number_'.$createNewItem->item->label;
        }

        /**
         * @var $numbering Attachment|null
         */
        $numbering = Attachment::query()
            ->where('title', $title)
            ->latest()
            ->select('id')
            ->first();

        if ($numbering !== null) {
            $createNewItem->item->attachments()->attach($numbering->id, [
                'order' => 0
            ]);
        } elseif (Attachment::query()->where('title', 'beep')->exists()) {
            $numbering = Attachment::query()->where('title', 'beep')->select('id')->first();

            $createNewItem->item->attachments()->attach($numbering->id, [
                'order' => 0
            ]);
        }
    }
}
