<?php

namespace Tests\Feature;

use Database\Seeders\ExamTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Entities\Account\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamSeederTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testHasPastExam()
    {
        $this->basicTest('past');
    }

    public function testHasFutureExam()
    {
        $this->basicTest('future');
    }

    public function testHasRunningExam()
    {
        $this->basicTest('running');
    }

    /**
     * A basic feature test example.
     *
     * @param $state
     * @return void
     */
    public function basicTest($state)
    {
        $this->login();
        $this->seed(ExamTableSeeder::class);

        $response = $this->get('/api/client/exam?state='.$state, [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $exams = $response->json();
        $this->assertTrue($exams['data'] > 0);
    }

    private function login()
    {
        /**
         * @var $student User
         */
        $student = User::whereIs('student')->first();
        $this->actingAs($student, 'api');
    }
}
