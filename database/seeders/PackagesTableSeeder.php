<?php

namespace Database\Seeders;

use App\Jobs\Package\CreateNewPackage;
use Illuminate\Database\Seeder;

class PackagesTableSeeder extends Seeder
{
    public function run()
    {
        force_queue_sync(function () {
            dispatch_now(new CreateNewPackage([
                'title' => 'Example Package',
            ]));
        });
    }
}
