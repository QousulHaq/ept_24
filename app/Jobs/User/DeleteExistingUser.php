<?php

namespace App\Jobs\User;

use App\Entities\Account\User;
use Jalameta\Support\Bus\BaseJob;

class DeleteExistingUser extends BaseJob
{
    public User $user;

    /**
     * Job constructor.
     *
     * @param User $user
     * @param array $inputs
     */
    public function __construct(User $user, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->user = $user;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function run(): bool
    {
        $username = $this->user->getAttribute('username').'-'.$this->user->getAttribute('id');
        $email = $this->user->getAttribute('email').'-'.$this->user->getAttribute('id');

        $this->user->update(['username' => $username, 'email' => $email]);

        return $this->user->delete();
    }
}
