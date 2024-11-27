<?php

namespace App\Providers;

use App\Entities\Account\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes(['middleware' => ['api', 'auth:api']]);

        Broadcast::channel('back-office', function ($user) {
            return $user;
        }, ['guards' => 'api']);

        Broadcast::channel('client', function ($user) {
            return $user;
        }, ['guards' => 'api']);

        Broadcast::channel('notification.{username}', function ($user, $username) {
            abort_if($user->username !== $username, 403);

            return $user;
        });

        Broadcast::channel('attendance', function ($user) {
            return ($user instanceof User)
                ? $user->fresh(['roles'])->toArray()
                : null;
        }, ['guards' => 'api']);

        Broadcast::channel('exam.{examId}', function (User $user, $examId) {
            abort_if((! $user->canParticipate($examId) && ! $user->canAny(['exam.manage', 'exam.result.show'])), 403);

            return $user->fresh(['exams' => fn (BelongsToMany $builder) => $builder->wherePivot('exam_id', $examId)]);
        }, ['guards' => 'api']);
    }
}
