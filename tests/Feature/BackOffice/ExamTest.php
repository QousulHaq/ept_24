<?php

namespace Tests\Feature\BackOffice;

use Tests\TestCase;
use App\Entities\Account\User;
use App\Entities\Question\Package;
use Illuminate\Foundation\Testing\WithFaker;

class ExamTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateNewExam()
    {
        $package = Package::query()->first();

        /** @var $user User */
        $user = User::whereIs('superuser')->firstOrFail();
        $students = User::whereIs('student')->get();

        $this->assertNotNull($package);
        $this->assertNotNull($user);

        $this->actingAs($user, 'api');

        $response = $this->post(route('api.back-office.exam.store'), [
            'package_id' => $package->id,
            'name' => $this->faker->text,
            'scheduled_at' => now()->addMinutes(3),
            'participants' => $students->map->hash->toArray(),
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
    }
}
