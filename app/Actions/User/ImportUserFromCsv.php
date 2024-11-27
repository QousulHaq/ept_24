<?php

namespace App\Actions\User;

use App\Entities\Account\User;
use Faker\Factory;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use League\Csv\Reader;
use Silber\Bouncer\Bouncer;

class ImportUserFromCsv
{
    public function __construct(
        public Request $request,
        protected Bouncer $bouncer,
    ) {}

    /**
     * @throws \League\Csv\Exception
     * @throws \Illuminate\Validation\ValidationException
     */
    public function import(): Collection
    {
        $this->request->validate([
            'file' => 'required|file',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $file = $this->request->file('file');

        $csv = Reader::createFromString($file->getContent());

        if ($csv->count() === 0) {
            throw ValidationException::withMessages([
                'file' => ['The file is empty.'],
            ]);
        }
        $csv->setHeaderOffset(0);

        $data = collect($csv->getRecords())->values();
        $this->validateData($data);

        $faker = Factory::create();

        $syncUserRole = function (User $user) {
            $this->bouncer->sync($user)->roles($this->request->input('roles'));
        };

        $resetPassword = function (User $user) {
            $this->broker()->sendResetLink(['email' => $user->email]);
        };

        $data->transform(static function ($datum) use ($faker, $syncUserRole, $resetPassword) {
            $user = new User();
            $user->name = $datum['name'];
            $user->email = $datum['email'];
            $user->username = $datum['username'];
            $user->password = bcrypt($faker->password(8, 10));
            $user->alt_id = $datum['id'];
            $user->save();

            $syncUserRole($user);
            $resetPassword($user);

            return $user;
        });

        return $data;
    }

    private function validateData(Collection $data): void
    {
        Validator::make($data->toArray(), [
            '*.id' => 'nullable|string|max:36',
            '*.name' => 'required|string|max:255',
            '*.email' => 'required|string|email|max:255|unique:users',
            '*.username' => 'required|string|max:255|unique:users',
        ])->validate();
    }

    protected function broker(): PasswordBroker
    {
        return Password::broker(config('fortify.passwords'));
    }
}
