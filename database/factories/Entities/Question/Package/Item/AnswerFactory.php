<?php

namespace Database\Factories\Entities\Question\Package\Item;

use App\Entities\Question\Package\Item\Answer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition()
    {
        return [
            'content' => $this->faker->text(100),
        ];
    }
}

