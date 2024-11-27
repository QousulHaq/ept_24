<?php

use Jalameta\Patcher\Patch;

class FixPermissionUpdate extends Patch
{
    /**
     * Run patch script.
     *
     * @return void
     */
    public function patch()
    {
        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => \Database\Seeders\BouncerTableSeeder::class,
        ]);
    }
}
