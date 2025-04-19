<?php

namespace App\Extra\Presets\Cpns\Listeners;

use App\Extra\Presets\Cpns;
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
        Cpns::SECTION_TWK => [
            21, 22, 23, 23, 24, 25, 26, 27, 28, 28, 29, 30, 31, 32, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 43, 44,
            45, 46, 47, 48, 48, 49, 50, 51, 52, 52, 53, 54, 54, 55, 56, 57, 58, 59, 60, 61, 63, 65, 66, 67, 68,
        ],
        Cpns::SECTION_TIU => [
            21, 22, 23, 23, 24, 25, 26, 27, 28, 28, 29, 30, 31, 32, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 43, 44,
            45, 46, 47, 48, 48, 49, 50, 51, 52, 52, 53, 54, 54, 55, 56, 57, 58, 59, 60, 61, 63, 65, 66, 67, 68,
        ],
        Cpns::SECTION_TKP => [
            21, 22, 23, 23, 24, 25, 26, 27, 28, 28, 29, 30, 31, 32, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 43, 44,
            45, 46, 47, 48, 48, 49, 50, 51, 52, 52, 53, 54, 54, 55, 56, 57, 58, 59, 60, 61, 63, 65, 66, 67, 68,
        ],
    ];
    public function handle(SectionEnded $event)
    {
        if (! $event->section->preset instanceof Cpns) {
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
