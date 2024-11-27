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
