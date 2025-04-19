<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DevelopmentSeeder::class);
        $this->call(BouncerTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PackagesTableSeeder::class);

        $this->call(AttachmentsTableSeeder::class);

        $this->call(ETeflSeeder::class);
        $this->call(CpnsSeeder::class);
        $this->call(SmanSeeder::class);

        if (! app()->runningUnitTests()) {
            $this->call(ExamTableSeeder::class);
        }
    }
}
