<?php

namespace Database\Seeders;

use Closure;
use App\Jobs\CBT\Ticking;
use App\Entities\CBT\Exam;
use App\Entities\Account\User;
use App\Jobs\Exam\StartAnExam;
use Illuminate\Database\Seeder;
use App\Events\Exam\ExamStarted;
use App\Jobs\Exam\CreateNewExam;
use App\Entities\CBT\Participant;
use Jalameta\Support\Bus\BaseJob;
use Illuminate\Support\Facades\Event;
use App\Jobs\CBT\Section\StartSection;
use App\Jobs\CBT\EnrollExamForParticipant;
use Illuminate\Validation\ValidationException;
use App\Jobs\CBT\Section\Item\Attempt\UpdateExistingAttempt;

class ExamTableSeeder extends Seeder
{
    private static bool $complete;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createScheduledExam();

        Event::listen(ExamStarted::class, function (ExamStarted $event) {
            $event->exam->participants->each(function (User $user) use ($event) {
                auth('api')->setUser($user);
                dispatch_sync(new EnrollExamForParticipant($event->exam));
                $user->detail->sections->each(function (Participant\Section $section) use ($user) {
                    $items = $section->items->whereNull('tags');
                    $bar = $this->command->getOutput()->createProgressBar($items->count());

                    dispatch_sync(new StartSection($section));
                    $section = $section->fresh();

                    $items->each(function (Participant\Section\Item $item) use ($section, $bar) {
                        $answer = $item->reference->answers->where('correct_answer', true)->first();
                        /**
                         * @var $attempt \App\Entities\CBT\Participant\Section\Item\Attempt
                         */
                        $attempt = $item->attempts()->where('attempt_number', $section->attempts)->first();
                        BaseJob::$resetRequests = true;
                        dispatch_sync(new UpdateExistingAttempt(
                            $section,
                            $item,
                            $attempt,
                            [
                                'answer' => optional($answer)->content,
                            ]
                        ));

                        // ended item now...
                        if ($section->item_duration) {
                            dispatch_sync(new Ticking($item, [
                                'amount' => $item->getRemainingTime(),
                            ]));
                        }
                        BaseJob::$resetRequests = false;

                        $bar->advance();
                    });
                    $bar->finish();
                    $this->command->info(
                        " => [{$user->name}] all items from {$section->config['title']} answered!");

                    // ended section now...
                    if (! $section->item_duration) {
                        dispatch_sync(new Ticking($section, [
                            'amount' => $section->getRemainingTime() - (self::$complete ? 0 : 300),
                        ]));
                    }
                });
            });
        });

        $this->createFinishedExam();
        $this->createAlmostFinishExam();
    }

    public static function setTestEnv(Closure $callback)
    {
        $target = 'broadcasting.default';

        $original = config($target);
        config()->set($target, env('APP_ENV') === 'local' ? 'log' : 'null');
        force_queue_sync($callback);
        config()->set($target, $original);
    }

    public static function bumpExecuteJob(Closure $onSuccess)
    {
        request()->setUserResolver(fn () => User::whereIs('superuser')->firstOrFail());

        try {
            /**
             * @var $job CreateNewExam
             */
            $job = app(CreateNewExam::class);
            $job->onSuccess($onSuccess);

            dispatch_sync($job);

            return $job;
        } /* @noinspection PhpRedundantCatchClauseInspection */ catch (ValidationException $exception) {
            dump(request()->all());
            dump($exception->errors());

            return null;
        }
    }

    public function createAlmostFinishExam()
    {
        $this->command->warn('Creating exam that almost finish...');
        $this->createAnsweredExam(false);
    }

    public function createFinishedExam()
    {
        $this->command->warn('Creating exam that finished...');
        $this->createAnsweredExam();
    }

    /**
     * @param bool $complete
     */
    public function createAnsweredExam($complete = true)
    {
        self::$complete = $complete;

        self::setTestEnv(function () {
            Exam::factory()->count(1)->make()->each(function (Exam $exam, int $index) {
                if ($index === 0) {
                    $exam->setAttribute('scheduled_at', now()->subHours(4)->format('Y-m-d H:i:s'));
                    $exam->setAttribute('started_at', now()->subHours(4)->format('Y-m-d H:i:s'));
                }

                /* @noinspection PhpUndefinedFieldInspection */
                request()->replace(array_merge($exam->toArray(), [
                    'participants' => User::query()->where('username', 'student')->get()->map->hash
                        ->toArray(),
                ]));

                $job = self::bumpExecuteJob(
                    fn () => $this->command->info('=> Exam '.$exam->getAttribute('name').' has been ready'));

                if ($job instanceof CreateNewExam) {
                    dispatch_sync(new StartAnExam($job->exam));
                }
            });
        });
    }

    private function createScheduledExam()
    {
        $this->command->warn('Creating scheduled exams...');

        self::setTestEnv(function () {
            Exam::factory()->count(2)->make()->each(function (Exam $exam, int $index) {
                if ($index === 0) {
                    $exam->setAttribute('scheduled_at', now()->subMinute()->format('Y-m-d H:i:s'));
                }

                request()->replace($exam->toArray());
                $this->command->warn('=> Creating exam with name : '.$exam->getAttribute('name'));
                self::bumpExecuteJob(
                    fn () => $this->command->info('=> Exam '.$exam->getAttribute('name').' has been ready'));
            });
        });
    }
}
