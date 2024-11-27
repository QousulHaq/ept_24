<?php

namespace App\Http\Routes\BackOffice\Exams;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\BackOffice\Exams\ScheduleController;

class ScheduleRoute extends BaseRoute
{
    protected string $prefix = 'schedule';

    protected string $name = 'schedule';

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix, [
            'as' => $this->name('index'),
            'uses' => $this->uses('index'),
        ]);

        $this->router->get($this->prefix('create'), [
            'as' => $this->name('create'),
            'uses' => $this->uses('create'),
        ]);

        $this->router->get($this->prefix('{exam}/detail'), [
            'as' => $this->name('detail'),
            'uses' => $this->uses('show'),
        ]);

        $this->router->get($this->prefix('{exam}/edit'), [
            'as' => $this->name('edit'),
            'uses' => $this->uses('edit'),
        ]);
    }

    /**
     * Controller used by this route.
     *
     * @return string
     */
    public function controller(): string
    {
        return ScheduleController::class;
    }
}
