<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company' => $this->faker->company(),
            'vat' => $this->faker->randomNumber(5, true),
            'address' => $this->faker->address(),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
