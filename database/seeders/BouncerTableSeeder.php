<?php

namespace Database\Seeders;

use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Role;
use Silber\Bouncer\Database\Ability;

class BouncerTableSeeder extends Seeder
{
    public static array $data = [
        [
            'name' => 'superuser',
            'title' => 'Administrator Application',
            'abilities' => [
                [
                    'name' => 'package.manage',
                    'title' => 'Manage package questions',
                ],
                [
                    'name' => 'attachment.manage',
                    'title' => 'Manage attachment',
                ],
                [
                    'name' => 'quiz.manage',
                    'title' => 'Quiz users',
                ],
                [
                    'name' => 'user.manage',
                    'title' => 'Manage user. included create, edit and delete',
                ],
                [
                    'name' => 'exam.result.show',
                    'title' => 'Show Result of Exam',
                ],
                [
                    'name' => 'exam.result.update',
                    'title' => 'Update Result of Exam',
                ],
                [
                    'name' => 'exam.manage',
                    'title' => 'Create Exam Session, Modify Exam such as destroy session student, reset login student, Delete exam',
                ],
                [
                    'name' => 'client.manage',
                    'title' => 'Manage OAuth2 client server',
                ],
            ],
        ],
        [
            'name' => 'manager',
            'title' => 'Manager',
            'abilities' => [
                [
                    'name' => 'package.manage',
                    'title' => 'Manage package questions',
                ],
                [
                    'name' => 'quiz.manage',
                    'title' => 'Quiz users',
                ],
                [
                    'name' => 'user.manage',
                    'title' => 'Manage user. included create, edit and delete',
                ],
            ],
        ],
        [
            'name' => 'teacher',
            'title' => 'Teacher',
            'abilities' => [
                [
                    'name' => 'package.manage',
                    'title' => 'Manage package questions',
                ],
                [
                    'name' => 'quiz.manage',
                    'title' => 'Quiz users',
                ],
                [
                    'name' => 'exam.result.show',
                    'title' => 'Show Result of Exam',
                ],
                [
                    'name' => 'exam.result.update',
                    'title' => 'Update Result of Exam',
                ],
            ],
        ],
        [
            'name' => 'proctor',
            'title' => 'Proctor Exam',
            'abilities' => [
                [
                    'name' => 'exam.manage',
                    'title' => 'Create Exam Session, Modify Exam such as destroy session student, reset login student, Delete exam',
                ],
            ],
        ],
        [
            'name' => 'student',
            'title' => 'Student',
            'abilities' => [
                [
                    'name' => 'quiz.participate',
                    'title' => 'Participate to quiz',
                ],
            ],
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::$data as $role) {
            /**
             * @var Role $roleModel
             */
            $roleModel = Role::query()->firstOrCreate(Arr::only($role, 'name'), Arr::only($role, ['name', 'title']));

            $abilities = collect();
            foreach ($role['abilities'] as $ability) {
                $abilities->add(Ability::query()->firstOrCreate(Arr::only($ability, 'name'), $ability));
            }

            $roleModel->abilities()->sync($abilities->map->id);
        }
    }
}
