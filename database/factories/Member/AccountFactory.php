<?php

namespace Database\Factories\Member;

use App\Models\Member\Account;
use App\Models\Member\Contact;
use App\Services\AccountServices;
use App\Services\CommonServices;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $finanicalYear = CommonServices::getFinancialYear();
        return [
            "registration_number" =>
                "MEM" . $this->faker->randomNumber(5, true),
            "contact_id" => Contact::factory(),
            "due_date" => $this->faker
                ->dateTimeBetween("now", "+07 days")
                ->format("m/d/Y"),
            "outstanding_payment" => 1080,
            "financial_year" => $finanicalYear,
            "status" => 1,
        ];
    }
}
