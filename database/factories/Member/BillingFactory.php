<?php

namespace Database\Factories\Member;

use App\Models\Billing;
use App\Services\CommonServices;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Billing::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $finanicalYear = CommonServices::getFinancialYear();
        $start_date = $this->faker
            ->dateTimeBetween("now", "now")
            ->format("m/d/Y");
        $end_date = $this->faker
            ->dateTimeBetween("+30 days", "+30 days")
            ->format("m/d/Y");

        $billing_period = $start_date . "-" . $end_date;

        return [
            "bill_number" => "BIL" . $this->faker->randomNumber(5, true),
            "account_id" => 1,
            "status_code" => 2,
            "bill_issued_date" => $this->faker
                ->dateTimeBetween("now", "now")
                ->format("m/d/Y"),
            "bill_due_date" => $this->faker
                ->dateTimeBetween("+07 days", "+07 days")
                ->format("m/d/Y"),
            "bill_amount" => 1200,
            "prev_due_amount" => 0, // new registration there will be zero outstanding
            "plan_id" => 1, // plan id from request
            "financial_year" => $finanicalYear,
            "billing_period" => $billing_period, //billing period (based on subscription start and end date) in billing tabl
        ];
    }
}
