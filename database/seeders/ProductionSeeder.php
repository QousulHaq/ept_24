<?php

namespace Database\Seeders;

use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(DevelopmentSeeder::class);
        $this->call(BouncerTableSeeder::class);

        // create only user admin
        UsersTableSeeder::$data = Arr::only(UsersTableSeeder::$data, [0]);
        $this->call(UsersTableSeeder::class);

        // pack of package and question
        $this->call(PackagesTableSeeder::class);
        $this->call(AttachmentsTableSeeder::class);
        $this->call(ETeflSeeder::class);
    }
}
