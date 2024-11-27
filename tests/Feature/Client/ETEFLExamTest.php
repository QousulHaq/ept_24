<?php

namespace Tests\Feature\Client;

use Database\Seeders\ExamTableSeeder;
use Tests\TestCase;
use Illuminate\Support\Arr;
use App\Entities\Account\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ETEFLExamTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     * @throws \Exception
     */
    public function testWorkingWithExam()
    {
        $this->login();
        $this->seed(ExamTableSeeder::class);

        $response = $this->get('/api/client/exam?state=running', [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);

        $exams = $response->json();

        $this->assertTrue($exams['data'] > 0);

        $this->enroll($exams);
    }

    private function login()
    {
        /**
         * @var $student User
         */
        $student = User::whereIs('student')->first();
        $this->actingAs($student, 'api');
    }

    private function enroll(array $exams)
    {
        $token = $this->post('api/client/exam/'.$exams['data'][0]['id'].'/enroll', [
            'Accept' => 'application/json',
        ]);

        $token->assertStatus(200);
        $token->assertJsonStructure(['status', 'data' => ['signature', 'expires_in']]);

        $this->getSection($token->json());
    }

    private function getSection(array $token)
    {
        $response = $this->get('/api/client/section', [
            'Accept' => 'application/json',
            'X-Signature-Enroll' => $token['data']['signature'],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'sections']);

        $this->startAttemptSections($token, $response->json());
    }

    private function startAttemptSections(array $token, array $response)
    {
        foreach ($response['sections'] as $section) {
            // attempt start for section
            $response = $this->post('api/client/section/'.$section['id'].'/start', [], [
                'Accept' => 'application/json',
                'X-Signature-Enroll' => $token['data']['signature'],
            ]);

            // secondary request
            $responseSecond = $this->post('api/client/section/'.$section['id'].'/start', [], [
                'Accept' => 'application/json',
                'X-Signature-Enroll' => $token['data']['signature'],
            ]);

            $response->assertStatus(200);
            $responseSecond->assertStatus(200);
            $structure = [
                'status',
                'message',
                'data' => [
                    'last_attempted_at',
                    'ended_at',
                    'attempts',
                ],
            ];
            $response->assertJsonStructure($structure);
            $responseSecond->assertJsonStructure($structure);

            // get a section
            $response = $this->get('api/client/section/'.$section['id'], [
                'Accept' => 'application/json',
                'X-Signature-Enroll' => $token['data']['signature'],
            ]);

            $response->assertStatus(200);
            $response->assertJsonStructure([
                'last_attempted_at',
                'ended_at',
                'attempts',
                'item_duration',
                'items' => [
                    [
                        'id',
                        'config',
                        'type',
                        'content',
                        'attachments',
                        'remaining_time',
                        'order',
                        'answers',
                        'attempts' => [
                            [
                                'answer',
                                'attempt_number',
                            ],
                        ],
                    ],
                ],
            ]);

            $this->tryTickingSectionOrItem($response->json(), $token);
            $this->tryAnswerQuestion($response->json(), $token);
        }
    }

    private function tryTickingSectionOrItem(array $section, array $token)
    {
        $headers = [
            'Accept' => 'application/json',
            'X-Signature-Enroll' => $token['data']['signature'],
        ];

        if (! $section['item_duration']) {
            $lastDuration = $section['remaining_time'];

            $response = $this->patch('api/client/section/'.$section['id'].'/tick', [], $headers);

            $response->assertStatus(200);
            $response->assertJsonStructure(['status', 'message', 'data' => ['remaining_time']]);
            $this->assertTrue($lastDuration - $response->json('data.remaining_time') === 1);
        } else {
            $itemSample = $section['items'][0];
            $lastDuration = $itemSample['remaining_time'];

            while ($lastDuration >= 0) {
                $response = $this->patch('api/client/section/'.$section['id'].'/item/'.$itemSample['id'].'/tick', [], $headers);

                ($lastDuration !== 0)
                    ? $response->assertStatus(200)
                    : $response->assertStatus(412);

                $response->assertJsonStructure(['status', 'message', 'data' => ['remaining_time']]);

                $remainingTime = $response->json('data.remaining_time');

                if ($response->status() === 200) {
                    $this->assertTrue($lastDuration - $remainingTime === 1);
                } else {
                    break;
                }

                $lastDuration = $remainingTime;
            }
        }
    }

    private function tryAnswerQuestion(array $section, array $token)
    {
        $item = Arr::first(Arr::random(Arr::where($section['items'], fn ($item) => $item['label'] !== '#'), 1));
        $this->assertLessThanOrEqual(1, count($item['attempts']));

        $attempt = Arr::first($item['attempts']);
        $answer = Arr::first($item['answers']);

        $response = $this->put('api/client/section/'.$section['id'].'/item/'.$item['id'].'/attempt/'.$attempt['id'], [
            'item_answer_id' => $answer['id'],
        ], [
            'Accept' => 'application/json',
            'X-Signature-Enroll' => $token['data']['signature'],
        ]);

        $response->assertStatus(200);
        $this->assertSame($answer['content'], $response->json('data.answer'));
    }
}
