<?php

namespace App\Jobs\CBT;

use App\Entities\CBT\Exam;
use Illuminate\Support\Arr;
use App\Entities\Account\User;
use App\Entities\CBT\Participant;
use Jalameta\Support\Bus\BaseJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\CBT\WantedToNextSectionOrItem;

class ExamTicker extends BaseJob implements ShouldQueue
{
    const GAP = 5;

    /**
     * @var \App\Entities\CBT\Exam
     */
    public Exam $exam;

    public $queue = 'ticker';

    private bool $continue = false;
    private bool $isFirst;

    public function __construct(Exam $exam, bool $isFirst = true, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->exam = $exam;
        $this->isFirst = $isFirst;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['ticker', 'exam:'.$this->exam->id];
    }

    public function run(): bool
    {
        /* @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->exam = $this->exam->fresh(['participants']);
        $this->exam->participants->each(fn (User $user) => $this->processParticipant($user));

        // recursive job until all participant done
        if ($this->continue) {
            dispatch(new self($this->exam, false))->delay(now()->addSeconds(self::GAP));
        }

        return true;
    }

    private function processParticipant(User $user)
    {
        /**
         * @var $participant Participant
         */
        $participant = $user->getAttribute('detail');

        if ($participant->getAttribute('status') === Participant::STATUS_ACTIVE) {
            /**
             * @var $section \App\Entities\CBT\Participant\Section
             */
            $section = $participant->sections->sortBy(fn ($s) => Arr::get($s, 'config.order'))
                ->firstWhere('ended_at', '==', null);

            // all section has done, do nothing
            if (! $section) {
                return;
            }

            if (! $section->getAttribute('item_duration')) {
                // tick section
                $job = new Ticking($section, ['amount' => $this->isFirst
                    ? self::GAP
                    : now()->diffInSeconds($section->getAttribute('updated_at')), ]);
                dispatch_sync($job);

                if ($job->status === BaseJob::STATUS_SUCCESS) {
                    $this->informUser($user, $section);
                }
            } else {
                // tick item
                /**
                 * @var $item \App\Entities\CBT\Participant\Section\Item|null
                 */
                $item = $section->items->sortBy(fn ($_item) => $_item->order)
                    ->first(fn (Participant\Section\Item $_item) => $_item->getRemainingTime() !== 0);

                if ($item === null) {
                    $this->informUser($user, $section);
                } else {
                    $job = new Ticking($item, [
                        'amount' => $this->isFirst
                            ? self::GAP
                            : now()->diffInSeconds($item->getAttribute('updated_at')),
                    ]);
                    dispatch_sync($job);

                    if ($job->status === BaseJob::STATUS_SUCCESS) {
                        $this->informUser($user, $section, $item);
                    }
                }
            }

            // decide loop continue if still have a section with ended_at is null
            if ($participant->sections()->whereNull('ended_at')->count() > 0) {
                $this->continue = true;
            }
        }
    }

    private function informUser(
        User $user,
        Participant\Section $section,
        ?Participant\Section\Item $item = null)
    {
        /**
         * @var $model \App\Extra\Contracts\HasRemainingTime
         */
        $model = $item ?? $section;

        if ($model->getRemainingTime() === 0) {
            $user->notify(new WantedToNextSectionOrItem($model));
        } else {
            // $user->notify(new SendRemainingTime($model));
        }
    }
}
