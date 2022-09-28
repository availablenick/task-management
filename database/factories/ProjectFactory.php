<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = [Project::OPEN_STATUS, Project::CLOSED_STATUS];
        return [
            'title' => $this->faker->words(2, true),
            'description' => $this->faker->paragraph(),
            'deadline' => $this->faker->date(),
            'status' => $status[array_rand($status, 1)],
        ];
    }

    public function closed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Project::CLOSED_STATUS,
            ];
        });
    }
}
