<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentAlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'is_noted' => false,
        ];
    }

    public function noted()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_noted' => true,
            ];
        });
    }
}
