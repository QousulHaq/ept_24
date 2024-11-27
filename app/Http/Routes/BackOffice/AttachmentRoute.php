<?php

namespace App\Http\Routes\BackOffice;

use App\Http\Controllers\BackOffice\AttachmentController;
use Dentro\Yalr\BaseRoute;

class AttachmentRoute extends BaseRoute
{
    /**
     * Route path prefix.
     *
     * @var string
     */
    protected string $prefix = 'attachment';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected string $name = 'attachment';

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix, [
            'as' => $this->name,
            'uses' => $this->uses('index')
        ]);
    }

    public function controller(): string
    {
        return AttachmentController::class;
    }
}
