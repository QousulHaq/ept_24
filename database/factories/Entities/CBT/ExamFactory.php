<?php

namespace Database\Factories\Entities\CBT;

use Carbon\Carbon;
use App\Entities\CBT\Exam;
use App\Entities\Account\User;
use App\Entities\Question\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition()
    {
        return [
            'package_id' => Package::query()->first(),
            'name' => 'TEST/'.Carbon::now()->format('Y/m/d')."/{$this->faker->randomDigit}",
            'scheduled_at' => now()->addMinutes(3),
            'is_anytime' => false,
            'participants' => User::whereIs('student')->take(35)->get()->map->hash->toArray(),
        ];
    }
}

