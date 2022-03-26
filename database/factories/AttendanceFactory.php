<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "account_id" => 1,
            "attendance_date" => $this->faker
                ->dateTimeBetween("-120 days", "now")
                ->format("m/d/Y"),
            "in_time" => $this->faker->time('H:i:s'),
        ];
    }
}
