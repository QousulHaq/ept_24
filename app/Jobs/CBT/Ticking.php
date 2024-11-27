<?php

namespace App\Jobs\CBT;

use Illuminate\Http\Response;
use Jalameta\Support\Bus\BaseJob;
use App\Entities\CBT\Participant\Section;
use App\Events\Exam\Section\SectionEnded;
use App\Extra\Contracts\HasRemainingTime;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response as SymponyResponse;

class Ticking extends BaseJob implements Responsable
{
    public const ERROR_MESSAGE_EMPTY_TIME = 'remaining time is 0';

    /**
     * @var HasRemainingTime
     */
    public HasRemainingTime $model;

    public function __construct(HasRemainingTime $model, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->model = $model;

        $this->onSuccess(function () {
            if ($this->model instanceof Section && $this->model->getRemainingTime() === 0) {
                $this->model->setAttribute('ended_at', now());
                $this->model->save();
                event(new SectionEnded($this->model));
            } elseif ($this->model instanceof Section\Item && $this->model->getRemainingTime() === 0) {
                $queryNeighbour = Section\Item::query()
                    ->where('section_id', $this->model->getAttribute('section_id'))
                    ->orderByDesc('order');

                // exorcism the item before this
                $queryNeighbour->clone()
                    ->where('order', '<', $this->model->getAttribute('order'))
                    ->where('remaining_time', '>', 0)
                    ->update([
                        'remaining_time' => 0,
                    ]);

                // checking thing
                $lastItemOrderNeighbour = (int) $queryNeighbour->first(['order'])->order;

                $isLast = $lastItemOrderNeighbour === $this->model->order;

                // is item duration
                if ($isLast) {
                    $this->model->section->setAttribute('ended_at', now());
                    $this->model->section->save();
                    event(new SectionEnded($this->model->section));
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function run(): bool
    {
        if ($this->model->getRemainingTime() === 0) {
            return false;
        }

        $amount = $this->request->input('amount', 1);

        if ($amount > $this->model->getRemainingTime()) {
            $amount = $this->model->getRemainingTime();
        }

        return $this->model->decrementRemainingTime($amount);
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        return new Response([
            'status' => $this->status,
            'message' => $this->status === self::STATUS_FAILED ? self::ERROR_MESSAGE_EMPTY_TIME : '',
            'data' => $this->model->fresh()?->only(['id', 'remaining_time']),
        ], $this->status === self::STATUS_SUCCESS
            ? SymponyResponse::HTTP_OK
            : SymponyResponse::HTTP_PRECONDITION_FAILED);
    }
}
