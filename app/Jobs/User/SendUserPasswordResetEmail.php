<?php

namespace App\Jobs\User;

use App\Entities\Account\User;
use App\Notifications\User\ResetPassword;
use Illuminate\Auth\Passwords\PasswordBroker;
use Jalameta\Support\Bus\BaseJob;

class SendUserPasswordResetEmail extends BaseJob
{
    public User $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct([]);

        $this->user = $user;
    }

    public function run(): bool
    {
        $token = app(PasswordBroker::class)->createToken($this->user);
        $this->user->notify(new ResetPassword($token));

        return true;
    }
}
