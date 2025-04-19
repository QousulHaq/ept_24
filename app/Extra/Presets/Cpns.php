<?php

namespace App\Extra\Presets;

use App\Entities\CBT\Participant;
use App\Extra\CBT;
use App\Extra\Contracts\Preset;
use App\Entities\Question\Package\Item;

class Cpns implements Preset
{
    public const NAME = 'CPNS';

    public const SECTION_TWK = 'Wawasan Kebangsaan';
    public const SECTION_TIU = 'Intelegensia Umum';
    public const SECTION_TKP = 'Karakteristik Pribadi';

    public static array $subPresetTree = [
        [
            'config' => self::NAME.'.twk',
            'intro_code' => 'INTRODUCTION_TWK',
            'title' => self::SECTION_TWK,
            'order' => 1,
            'duration' => 3300,
            'random_item' => true,
            'item_total' => 5,
            'intro' => [
                'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
            ],
            'item' => [
                'type' => Item::TYPE_BUNDLE,
                'item_count' => 10,
                'extra' => ['line_count:5', 'width:41em'],
                'sub-item' => [
                    'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                    'answer_order_random' => true,
                    'answer_count' => 5,
                ],
            ],
        ],
        [
            'config' => self::NAME.'.tiu',
            'intro_code' => 'INTRODUCTION_TIU',
            'title' => self::SECTION_TIU,
            'order' => 2,
            'duration' => 3300,
            'random_item' => true,
            'item_total' => 5,
            'intro' => [
                'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
            ],
            'item' => [
                'type' => Item::TYPE_BUNDLE,
                'item_count' => 10,
                'extra' => ['line_count:5', 'width:41em'],
                'sub-item' => [
                    'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                    'answer_order_random' => true,
                    'answer_count' => 5,
                ],
            ],
        ],
        [
            'config' => self::NAME.'.tkp',
            'intro_code' => 'INTRODUCTION_TKP',
            'title' => self::SECTION_TKP,
            'order' => 3,
            'duration' => 3300,
            'random_item' => true,
            'item_total' => 5,
            'intro' => [
                'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
            ],
            'item' => [
                'type' => Item::TYPE_BUNDLE,
                'item_count' => 10,
                'extra' => ['line_count:5', 'width:41em'],
                'sub-item' => [
                    'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                    'answer_order_random' => true,
                    'answer_count' => 5,
                ],
            ],
        ],
    ];

    public function getName(): string
    {
        return self::NAME;
    }

    public function getCode(): string
    {
        return strtoupper(self::NAME);
    }

    public function getInfo(): string
    {
        return 'Ini adalah Tes CPNS';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'code' => $this->getCode(),
            'info' => $this->getInfo(),
        ];
    }

    public function registerConfig(CBT $cbt): void
    {
        $cbt->setConfig(self::NAME, $this->toArray());

        $callable = function ($presets) use ($cbt, &$callable) {
            foreach ($presets as $preset) {
                $cbt->setConfig($preset['config'], $preset);
                if (! empty($preset['sub-preset'])) {
                    $callable($preset['sub-preset']);
                }
            }
        };

        $callable(self::$subPresetTree);
    }

    public function getScore(Participant $participant): float
    {
        return $participant->sections()->sum('score') / ($participant->sections()->count() ?: 1) * 10;
    }
}
