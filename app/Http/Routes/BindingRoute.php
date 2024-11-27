<?php

namespace App\Http\Routes;

use App\Entities\CBT\Exam;
use App\Entities\Account\User;
use Dentro\Yalr\BaseRoute;
use App\Entities\CBT\Participant;
use App\Entities\Media\Attachment;
use App\Entities\Question\Package;
use App\Extra\Eloquent\Scopes\RootEntityScope;

class BindingRoute extends BaseRoute
{
    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    public function register(): void
    {
        $this->router->bind('package__', function ($value) {
            return Package::query()->withoutGlobalScope(RootEntityScope::class)->findOrFail($value);
        });

        $this->router->bind('user', function (string $value) {
            return User::query()->withTrashed()->find(User::hashToId($value));
        });

        $this->router->bind('attachment_uuid', function ($value) {
            return Attachment::query()->findOrFail($value);
        });

        $this->router->bind('item', function ($value) {
            return Package\Item::query()->findOrFail($value);
        });

        $this->router->bind('exam', function ($value) {
            return Exam::query()->findOrFail($value);
        });

        $this->router->bind('participant', function ($value) {
            return Participant::query()->findOrFail($value);
        });

        $this->router->bind('participant_section', function ($value) {
            return Participant\Section::query()->findOrFail($value);
        });

        $this->router->model('section_item', Participant\Section\Item::class);

        $this->router->bind('item_attempt', function ($value) {
//            dd($value);
            return Participant\Section\Item\Attempt::query()->findOrFail($value);
        });
    }
}
