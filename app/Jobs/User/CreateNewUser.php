<?php

namespace App\Jobs\User;

use App\Entities\Account\User;
use App\Events\User\UserCreated;
use Illuminate\Support\Facades\Validator;
use Silber\Bouncer\Bouncer;

class CreateNewUser
{
    public User $user;
    private array $inputs;

    /**
     * Create a new job instance.
     *
     * @param array $inputs
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __construct(array $inputs = [])
    {
        $this->user = new User();

        $this->inputs = Validator::make($inputs, [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
            'roles' => 'required|array',
            'alt_id' => 'nullable',
            'roles.*' => 'string|exists:roles,name',
            'image_id' => 'nullable|exists:attachments,id',
        ])->validate();
    }

    public function handle()
    {
        $this->user->fill($this->inputs);

        $this->user->save();

        if ($this->user->exists) {
            if (array_key_exists('image_id', $this->inputs)) {
                $this->user->attachments()->attach($this->inputs['image_id']);
            }

            app(Bouncer::class)->sync($this->user)->roles($this->inputs['roles']);
            event(new UserCreated($this->user));
        }
    }
}
