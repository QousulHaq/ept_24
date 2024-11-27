<?php

namespace App\Extra\Presets\ETefl\Listeners;

use App\Extra\Presets\ETefl;
use App\Entities\CBT\Participant\Section;
use App\Events\Exam\Section\SectionEnded;
use App\Events\Exam\Section\SectionScored;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Score finished section.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class CountScoreFinishedSection implements ShouldQueue
{
    const SCORE_BOARD = [
        ETefl::SECTION_LISTENING => [
            24, 25, 26, 27, 28, 29, 30, 31, 32, 32, 33, 35, 37, 38, 39, 41, 41, 42, 43, 44, 45, 45, 46, 47, 47, 48,
            48, 49, 49, 50, 51, 51, 52, 52, 53, 54, 54, 55, 56, 57, 57, 58, 59, 60, 61, 62, 63, 65, 66, 67, 68,
        ],
        ETefl::SECTION_GRAMMAR => [
            20, 20, 21, 22, 23, 25, 26, 27, 29, 31, 33, 35, 36, 37, 38, 40, 40, 41, 42, 43, 44,
            45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 60, 61, 63, 65, 67, 68,
        ],
        ETefl::SECTION_READING => [
            21, 22, 23, 23, 24, 25, 26, 27, 28, 28, 29, 30, 31, 32, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 43, 44,
            45, 46, 47, 48, 48, 49, 50, 51, 52, 52, 53, 54, 54, 55, 56, 57, 58, 59, 60, 61, 63, 65, 66, 67, 68,
        ],
    ];
    public function handle(SectionEnded $event)
    {
        if (! $event->section->preset instanceof ETefl) {
            return;
        }

        $rightAnswer = 0;

        $event->section->items()
            ->with('attempts', 'answers')
            ->whereNull('tags')
            ->cursor()
            ->each(function (Section\Item $item) use (&$rightAnswer, $event) {
                $attempt = $item->attempts->where('attempt_number', $event->section->attempts)->first();
                $answer = $item->answers->where('correct_answer', true)->first();
                $attempt->score = $answer ? (int) ($answer->content === $attempt->answer) : 0;

                if ($attempt->score == 1) {
                    $rightAnswer++;
                }

                $attempt->save();
            });

        $event->section->score = self::SCORE_BOARD[$event->section->config['title']][$rightAnswer];

        if ($event->section->save()) {
            event(new SectionScored($event->section));
        }
    }
}
