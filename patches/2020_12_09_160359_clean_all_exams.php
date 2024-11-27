<?php

use App\Entities\CBT\Exam;
use Jalameta\Patcher\Patch;

class CleanAllExams extends Patch
{
    /**
     * Run patch script.
     *
     * @return void
     * @throws \Exception
     */
    public function patch()
    {
        Exam::all()->each(function (Exam $exam) {
            $this->command->info('deleting exam '.$exam->name);
            $exam->delete();
        });
    }
}
