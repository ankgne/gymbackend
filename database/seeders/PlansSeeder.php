<?php

namespace Database\Seeders;

use App\Models\Member\Plan;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                "value" => "monthly",
                "name" => "Monthly@1,200",
                "fee" => 1200,
                "validity" => 30,
            ],
            [
                "value" => "3months",
                "name" => "3 Months@3,000",
                "fee" => 3000,
                "validity" => 90,
            ],
            [
                "value" => "6months",
                "name" => "6 Months@6,500",
                "fee" => 6500,
                "validity" => 180,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate($plan);
        }
    }
}
