<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', 'now');
        $endDate = fake()->optional(0.7)->dateTimeBetween($startDate, '+6 months');

        return [
            'project_name' => fake()->catchPhrase(),
            'description' => fake()->paragraph(3),
            'ownership_type' => fake()->randomElement(['Internal', 'Client', 'Freelance']),
            'link_testing' => fake()->optional(0.5)->url(),
            'link_prod' => fake()->optional(0.3)->url(),
            'status_id' => ProjectStatus::factory(),
            'client_id' => fake()->optional(0.6)->passthrough(Client::factory()),
            'created_by' => User::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
}
