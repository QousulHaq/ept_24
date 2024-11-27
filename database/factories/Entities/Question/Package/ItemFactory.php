<?php

namespace Database\Factories\Entities\Question\Package;

use App\Entities\Question\Package\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;


    public function definition()
    {
        return [
            'content' => $this->faker->text,
        ];
    }
}
