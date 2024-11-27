<?php

namespace App\Notifications\CBT;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

abstract class BaseNotification extends Notification
{
    // method is follow the antd method,
    // here https://www.antdv.com/components/notification/
    // or https://www.antdv.com/components/message/
    // we can create a custom method with create method on root vue instance with $ prefix
    protected string $method = 'message.info';

    // duration is optional.
    protected float $duration = 1.5;

    protected ?array $payload = null;

    public function via()
    {
        return ['broadcast'];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @return BroadcastMessage
     */
    public function toBroadcast()
    {
        $data = [
            'method' => $this->method,
        ];

        if ($this->payload !== null) {
            $data = array_merge($data, $this->payload);
        }

        return (new BroadcastMessage($data))->onQueue('broadcasts');
    }
}
