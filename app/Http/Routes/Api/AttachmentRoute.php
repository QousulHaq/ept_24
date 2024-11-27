<?php

namespace App\Http\Routes\Api;

use Dentro\Yalr\BaseRoute;
use App\Http\Controllers\Api\AttachmentController;

class AttachmentRoute extends BaseRoute
{
    /**
     * Route path prefix.
     *
     * @var string
     */
    protected string $prefix = '/attachment';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected string $name = 'back-office.attachment';

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->get($this->prefix('{attachment_uuid}.json'), [
            'as' => 'client.attachment.show.data',
            'uses' => $this->uses('showData'),
        ]);

        $this->router->get($this->prefix('{attachment_uuid}'), [
            'as' => 'client.attachment.show',
            'uses' => $this->uses('show'),
        ]);

        $this->router->patch($this->prefix('{attachment_uuid}'), [
            'as' => $this->name('update'),
            'uses' => $this->uses('update'),
        ])->middleware(['auth:api', 'role:superuser|proctor']);

        $this->router->delete($this->prefix('{attachment_uuid}'), [
            'as' => $this->name('destroy'),
            'uses' => $this->uses('destroy'),
        ])->middleware(['auth:api', 'role:superuser|proctor|manager']);

        $this->router->post($this->prefix, [
            'as' => $this->name,
            'uses' => $this->uses('store'),
        ])->middleware(['auth:api', 'role:superuser|proctor|manager']);
    }

    /**
     * Controller used by this route.
     *
     * @return string
     */
    public function controller(): string
    {
        return AttachmentController::class;
    }
}
