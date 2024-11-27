<?php

namespace App\Http\Routes\Api\BackOffice;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\Api\BackOffice\ExamController;

class ExamRoute extends BaseRoute
{
    protected string $prefix = 'back-office/exam';

    protected string $name = 'back-office';

    protected array|string $middleware = ['auth:api', 'role:superuser|manager|supervisor|teacher|proctor'];

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix('{exam}/start-exam'), [
            'as' => $this->name('exam.start-exam'),
            'uses' => $this->uses('startExam'),
        ]);

        $this->router->get($this->prefix('{exam}/end-exam'), [
            'as' => $this->name('exam.end-exam'),
            'uses' => $this->uses('endExam'),
        ]);

        $this->router->get($this->prefix('{exam}/start-exam/{user}'), [
            'as' => $this->name('exam.disqualified-participant'),
            'uses' => $this->uses('disqualifiedParticipant'),
        ]);

        $this->router->get($this->prefix('{exam}/start-exam/{user}/qualified'), [
            'as' => $this->name('exam.qualified-participant'),
            'uses' => $this->uses('qualifiedParticipant'),
        ]);

        $this->router->post($this->prefix('{exam}/participant/{user}/log'), [
            'as' => $this->name('exam.participant.log'),
            'uses' => $this->uses('storeLog'),
        ]);

        $this->router->patch($this->prefix('{exam}/decrypt'), [
            'as' => $this->name('exam.decrypt'),
            'uses' => $this->uses('decrypt'),
        ]);

        $this->router->apiResource($this->prefix, $this->controller(), [
            'as' => $this->name,
        ]);
    }

    /**
     * Controller used by this route.
     *
     * @return string
     */
    public function controller(): string
    {
        return ExamController::class;
    }
}
