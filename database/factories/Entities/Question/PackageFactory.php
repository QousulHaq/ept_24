<?php

namespace Database\Factories\Entities\Question;

use App\Entities\Question\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition()
    {
        return [
            'title' => $this->faker->word.' Test',
            'description' => $this->faker->realText(),
            'level' => $this->faker->randomElement([1, 2, 3, 4, 5]),
        ];
    }
}

