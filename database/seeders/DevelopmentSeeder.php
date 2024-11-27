<?php

namespace Database\Seeders;

use Laravel\Passport\Client;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Client::query()->create([
            'id' => env('MIX_VUE_APP_CLIENT_ID', 666),
            'name' => 'E TEFL EXAM CLIENT',
            'secret' => env('MIX_VUE_APP_CLIENT_SECRET', 'dBbYaKokFR3fB0NIGqLgiWHBhuOQezKNv5535g32'),
            'redirect' => env('MIX_VUE_APP_REDIRECT_URL', 'http://localhost:3000/client'),
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0,
        ]);

        Client::query()->create([
            'id' => 667,
            'name' => 'E TEFL EXAM PASSWORD CLIENT',
            'secret' => 'SJoXrLX9TQYzfrtIEEGsh4He6a2Ts9JGyBFKQgAt',
            'redirect' => env('MIX_VUE_APP_REDIRECT_URL', 'http://localhost:3000/client'),
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
        ]);
    }
}
