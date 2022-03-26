<?php

namespace Database\Factories\Member;

use App\Models\Transaction;
use App\Services\CommonServices;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $finanicalYear = CommonServices::getFinancialYear();
        return [
            "receipt_number" => "RCT" . $this->faker->randomNumber(5, true),
            "account_id" => 1,
            "bill_id" => 1,
            "transaction_mode" => 1,
            "transaction_type" => 0,
            "transaction_date" => $this->faker
                ->dateTimeBetween("now", "now")
                ->format("m/d/Y"),
            "transaction_amount" => 120,
            "due_amount_before_transaction" => 1200,
            "due_amount_after_transaction" => 1080,
            "transaction_comment" => 'Payment Made',
            "financial_year" => $finanicalYear,
        ];
    }
}
