<?php

namespace App\Extra\Presets;

use App\Entities\CBT\Participant;
use App\Extra\CBT;
use App\Extra\Contracts\Preset;
use App\Entities\Question\Package\Item;

class ETefl implements Preset
{
    public const NAME = 'E-TEFL';

    public const SECTION_LISTENING = 'Listening';
    public const SECTION_GRAMMAR = 'Structure & Written Expression';
    public const SECTION_READING = 'Reading';

    public static array $subPresetTree = [
        [
            'config' => self::NAME.'.listening',
            'title' => self::SECTION_LISTENING,
            'order' => 1,
            'item_duration' => true,
            'sub-preset' => [
                [
                    'config' => self::NAME.'.listening'.'.part-a',
                    'intro_code' => 'INTRODUCTION_PART_A',
                    'title' => 'PART A',
                    'item_duration' => true,
                    'random_item' => true,
                    'item_total' => 30,
                    'categories' => [
                        'Strategies',
                        'Who, What, Where',
                        'Negatives',
                        'Functions',
                        'Contrary Meaning',
                        'Idiomatic Language',
                    ],
                    'intro' => [
                        'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                        'extra' => ['audio'],
                        'duration' => 12,
                    ],
                    'item' => [
                        'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                        'extra' => ['audio', 'no_content'],
                        'answer_order_random' => true,
                        'duration' => 12,
                    ],
                ],
                [
                    'config' => self::NAME.'.listening'.'.part-b',
                    'intro_code' => 'INTRODUCTION_PART_B',
                    'title' => 'PART B',
                    'random_item' => true,
                    'item_duration' => true,
                    'item_total' => 2,
                    'intro' => [
                        'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                        'extra' => ['audio'],
                        'duration' => 12,
                    ],
                    'item' => [
                        'type' => Item::TYPE_BUNDLE,
                        'extra' => ['audio', 'no_content', 'time_audio_split'],
                        'item_count' => 4,
                        'duration' => 12,
                        'sub-item' => [
                            'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                            'extra' => ['audio', 'group_by:#,INTRODUCTION', 'no_content', 'time_audio_split'],
                            'answer_order_random' => true,
                            'duration' => 12,
                        ],
                    ],
                ],
                [
                    'config' => self::NAME.'.listening'.'.part-c',
                    'intro_code' => 'INTRODUCTION_PART_C',
                    'title' => 'PART C',
                    'random_item' => true,
                    'item_duration' => true,
                    'item_total' => 3,
                    'intro' => [
                        'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                        'extra' => ['audio'],
                        'duration' => 12,
                    ],
                    'item' => [
                        'type' => Item::TYPE_BUNDLE,
                        'extra' => ['audio', 'no_content', 'time_audio_split'],
                        'item_count' => 4,
                        'duration' => 12,
                        'sub-item' => [
                            'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                            'extra' => ['audio', 'group_by:#,INTRODUCTION', 'no_content', 'time_audio_split'],
                            'answer_order_random' => false,
                            'duration' => 12,
                        ],
                    ],
                ],
            ],
        ],
        [
            'config' => self::NAME.'.grammar',
            'title' => self::SECTION_GRAMMAR,
            'order' => 2,
            'duration' => 1500,
            'sub-preset' => [
                [
                    'config' => self::NAME.'.grammar'.'.structure',
                    'intro_code' => 'INTRODUCTION_STRUCTURE',
                    'title' => 'Structure',
                    'random_item' => true,
                    'item_total' => 15,
                    'categories' => [
                        'Sentences with One Clause',
                        'Sentences with Multiple Clause',
                        'More Sentences with Multiple Clause',
                        'Sentences with Reduced Clauses',
                        'Sentences with Inverted Subjects and Verbs',
                    ],
                    'intro' => [
                        'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                    ],
                    'item' => [
                        'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                        'answer_order_random' => true,
                    ],
                ],
                [
                    'config' => self::NAME.'.grammar'.'.written-expression',
                    'intro_code' => 'INTRODUCTION_WE',
                    'title' => 'Written Expression',
                    'random_item' => true,
                    'item_total' => 25,
                    'categories' => [
                        'Problems with Subject/Verb Agreement',
                        'Problems with Parallel Structure',
                        'Problems with Comparatives and Superlatives',
                        'Problems with the Form of the Verb',
                        'Problems with the Use of the Verb',
                        'Problems with Passive Verbs',
                        'Problems with Nouns',
                        'Problems with Pronouns',
                        'Problems with Adjectives and Adverbs',
                        'More Problems with Adjectives',
                        'Problems with Articles',
                        'Problems with Prepositions',
                        'Problems with Usage',
                    ],
                    'intro' => [
                        'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                    ],
                    'item' => [
                        'type' => Item::TYPE_MULTI_CHOICE_SINGLE,
                        'answer_order_random' => false,
                        'answer_count' => 0,
                        'extra' => ['answers_from_content', 'alphabet_counter_underline'],
                    ],
                ],
            ],
        ],
        [
            'config' => self::NAME.'.reading',
            'intro_code' => 'INTRODUCTION_READING',
            'title' => self::SECTION_READING,
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
        return 'E TEFL Preset Based on Longman Book';
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
