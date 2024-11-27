<?php

namespace App\Notifications\CBT;

use Illuminate\Database\Eloquent\Model;
use App\Extra\Contracts\HasRemainingTime;

class WantedToNextSectionOrItem extends BaseNotification
{
    protected string $method = 'events.next';

    /**
     * @var \App\Extra\Contracts\HasRemainingTime
     */
    private HasRemainingTime $model;

    public function __construct(HasRemainingTime $model)
    {
        $this->model = $model;

        $data = [];

        if ($this->model instanceof Model) {
            $data = array_merge($data, $this->model->only(['id', 'remaining_time']));
        }

        $this->payload = [
            'message' => 'Next..',
            'data' => $data,
        ];
    }
}
