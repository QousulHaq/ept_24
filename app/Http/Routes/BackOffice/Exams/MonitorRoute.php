<?php

namespace App\Http\Routes\BackOffice\Exams;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\BackOffice\Exams\MonitorController;

class MonitorRoute extends BaseRoute
{
    protected string $prefix = 'monitor';

    protected string $name = 'monitor';

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix('{exam}/detail'), [
            'as' => $this->name('detail'),
            'uses' => $this->uses('show'),
        ]);

        $this->router->get($this->prefix, [
            'as' => $this->name('index'),
            'uses' => $this->uses('index'),
        ]);

        $this->router->get($this->prefix('{exam}/start-exam'), [
            'as' => $this->name('start-exam'),
            'uses' => $this->uses('startExam'),
        ]);

        $this->router->get($this->prefix('{exam}/end-exam'), [
            'as' => $this->name('end-exam'),
            'uses' => $this->uses('endExam'),
        ]);

        $this->router->get($this->prefix('{exam}/start-exam/{user}'), [
            'as' => $this->name('disqualified-participant'),
            'uses' => $this->uses('disqualifiedParticipant'),
        ]);

        $this->router->get($this->prefix('{exam}/start-exam/{user}/qualified'), [
            'as' => $this->name('qualified-participant'),
            'uses' => $this->uses('qualifiedParticipant'),
        ]);

        $this->router->post($this->prefix('{exam}/participant/{user}/log'), [
            'as' => $this->name('participant.log'),
            'uses' => $this->uses('storeLog'),
        ]);

        $this->router->patch($this->prefix('{exam}/decrypt'), [
            'as' => $this->name('decrypt'),
            'uses' => $this->uses('decrypt'),
        ]);
    }

    /**
     * Controller used by this route.
     *
     * @return string
     */
    public function controller(): string
    {
        return MonitorController::class;
    }
}
