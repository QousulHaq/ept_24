<?php

namespace App\Notifications\CBT;

use Illuminate\Support\Arr;
use App\Extra\Contracts\HasRemainingTime;

class SendRemainingTime extends BaseNotification
{
    protected string $method = 'events.log';
    /**
     * @var \App\Extra\Contracts\HasRemainingTime|\Illuminate\Database\Eloquent\Model
     */
    private HasRemainingTime $model;

    public function __construct(HasRemainingTime $model)
    {
        $this->model = $model;
        $content = 'remaining_time ['.Arr::get($this->model, 'config.title').'] : '.$this->model->getRemainingTime();
        $this->payload = [
            'content' => $content,
        ];
    }
}
