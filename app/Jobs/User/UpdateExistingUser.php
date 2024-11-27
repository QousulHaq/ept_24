<?php

namespace App\Jobs\User;

use App\Entities\Account\User;
use Jalameta\Support\Bus\BaseJob;
use Silber\Bouncer\Bouncer;

class UpdateExistingUser extends BaseJob
{
    public User $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param array $inputs
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __construct(User $user, array $inputs = [])
    {
        parent::__construct($inputs);

        $this->user = $user;

        $this->inputs = $this->validate($this->request, [
            'name' => 'required_without:password',
            'username' => 'required_without:password|unique:users,username,'.$this->user->id,
            'email' => 'required_without:password|email|unique:users,email,'.$this->user->id,
            'roles' => 'required_without:password|array',
            'roles.*' => 'required_without:password|string|exists:roles,name',
            'alt_id' => 'nullable',
            'password' => 'required_without_all:name,username,email,roles|confirmed|min:8',
            'image_id' => 'nullable|exists:attachments,id',
        ]);
    }

    /**
     * Run the actual command process.
     *
     * @return bool
     */
    public function run(): bool
    {
        $this->user->fill($this->inputs);

        if (array_key_exists('image_id', $this->inputs)) {
            $this->onSuccess(fn () => $this->user->attachments()->attach($this->inputs['image_id']));
        }

        if (array_key_exists('roles', $this->inputs)) {
            $this->onSuccess(fn () => app(Bouncer::class)->sync($this->user)->roles($this->inputs['roles']));
        }

        return $this->user->save();
    }
}
