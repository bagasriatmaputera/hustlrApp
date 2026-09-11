<?php

namespace Database\Factories;

use App\Models\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectStatus>
 */
class ProjectStatusFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status_name' => fake()->randomElement(['Planning', 'In Progress', 'On Hold', 'Completed', 'Cancelled']),
        ];
    }
}
