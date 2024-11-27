<?php

return [
    'groups' => [
        'web' => [
            'middleware' => 'web',
            'prefix' => '',
        ],
        'api' => [
            'middleware' => 'api',
            'prefix' => 'api',
            'as' => 'api.',
        ],
        'back-office' => [
            'middleware' => [
                'web',
                'auth',
                'role:superuser|manager|supervisor|teacher|proctor',
                'api.token',
            ],
            'prefix' => 'back-office',
            'as' => 'back-office.',
        ],
        'api-client' => [
            'middleware' => ['api', 'api.client'],
            'prefix' => 'api/v1',
            'as' => 'api.open.'
        ],
    ],

    'web' => [
        App\Http\Routes\DefaultRoute::class,
        App\Http\Routes\AuthRoute::class,
        App\Http\Routes\PasswordRoute::class,
        App\Http\Routes\BackOffice\DefaultRoute::class,
        /** @inject web **/

        App\Http\Routes\BindingRoute::class,
    ],
    'back-office' => [
        App\Http\Routes\BackOffice\DashboardRoute::class,
        App\Http\Routes\BackOffice\AttachmentRoute::class,
        App\Http\Routes\BackOffice\PackageRoute::class,
        App\Http\Routes\BackOffice\Package\ItemRoute::class,
        App\Http\Routes\BackOffice\Exams\ScheduleRoute::class,
        App\Http\Routes\BackOffice\Exams\MonitorRoute::class,
        App\Http\Routes\BackOffice\Exams\HistoryRoute::class,
        App\Http\Routes\BackOffice\UserRoute::class,
        App\Http\Controllers\BackOffice\ClientController::class,
        /** @inject back-office **/
    ],
    'api' => [
        App\Http\Routes\Api\DefaultRoute::class,
        App\Http\Routes\Api\AttachmentRoute::class,
        App\Http\Routes\Api\BackOffice\Package\ItemRoute::class,
        App\Http\Routes\Api\BackOffice\ExamRoute::class,
        App\Http\Routes\Api\BackOffice\ClassificationRoute::class,
        App\Http\Routes\Api\BackOffice\ItemRoute::class,
        App\Http\Controllers\Api\BackOffice\PackageController::class,
        App\Http\Routes\Api\NotificationRoute::class,
        App\Http\Routes\Api\Client\ExamRoute::class,
        App\Http\Routes\Api\Client\SectionRoute::class,
        App\Http\Routes\Api\Client\Section\ItemRoute::class,
        App\Http\Controllers\Api\BackOffice\Package\DistributedController::class,
        App\Http\Routes\Api\BackOffice\UserRoute::class,
        /** @inject api **/
    ],
    'api-client' => [
        App\Http\Controllers\Client\KeyController::class,
        App\Http\Controllers\Client\PackageController::class,
        App\Http\Controllers\Client\ItemController::class,
        /** @inject api-client **/
    ],
];
