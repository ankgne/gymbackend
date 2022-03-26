<?php

namespace Database\Factories\Member;

use App\Models\Member\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "plan_id" => 1,
            "account_id" => 1,
            "start_date" => $this->faker
                ->dateTimeBetween("now", "now")
                ->format("m/d/Y"),
            "end_date" => $this->faker
                ->dateTimeBetween("+30 days", "+30 days")
                ->format("m/d/Y"),
            "charge" => 1200,
        ];
    }
}
