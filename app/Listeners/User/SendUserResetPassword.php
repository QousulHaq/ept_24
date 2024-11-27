<?php

namespace App\Listeners\User;

use App\Events\User\UserCreated;
use App\Jobs\User\SendUserPasswordResetEmail;

class SendUserResetPassword
{

    /**
     * Handle the event.
     *
     * @param \App\Events\User\UserCreated $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        dispatch_sync(new SendUserPasswordResetEmail($event->user));
    }
}
