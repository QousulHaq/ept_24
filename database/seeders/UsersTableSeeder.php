<?php

namespace Database\Seeders;

use Illuminate\Support\Arr;
use App\Entities\Account\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public static array $data = [
        [
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@etefl',
            'role' => 'superuser',
        ],
        [
            'name' => 'Teacher',
            'username' => 'teacher',
            'email' => 'teacher@etefl',
            'role' => 'manager,teacher,proctor',
        ],
        [
            'name' => 'Student',
            'username' => 'student',
            'email' => 'student@etefl',
            'role' => 'student',
        ],
        [
            'name' => 'Student01',
            'username' => 'student01',
            'email' => 'student01@etefl',
            'role' => 'student',
        ],
        [
            'name' => 'Student02',
            'username' => 'student02',
            'email' => 'student02@etefl',
            'role' => 'student',
        ],
        [
            'name' => 'Student03',
            'username' => 'student03',
            'email' => 'student03@etefl',
            'role' => 'student',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$data as $credential) {
            $user = User::query()->create(
                array_merge(Arr::except($credential, ['role']), ['password' => 'password'])
            );

            /**
             * @var User $user
             */
            $user->assign(explode(',', $credential['role']));
        }

//        User::factory(20)->create()->each(fn (User $user) => $user->assign('student'));
    }
}
