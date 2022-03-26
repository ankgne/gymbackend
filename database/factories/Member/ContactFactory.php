<?php

namespace Database\Factories\Member;

use App\Models\Member\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name(),
            "type" => 'customer',
            "gender" => $this->faker->randomElement(['male', 'female']),
            "dob" => $this->faker->date('m/d/Y'),
            "phone" => $this->faker->numerify('##########'),
            "email" => $this->faker->unique()->safeEmail(),
            "address" => $this->faker->address(),
            "city" => $this->faker->city(),
            "state" => $this->faker->randomElement(['AN', 'AP', 'AR', 'AS' , 'BR', 'PB']),
            "pincode" => 140606 ,
            "created_by" => 3,
        ];
    }
}
