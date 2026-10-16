<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['Backlog', 'In Progress', 'In Review', 'Done']),
            'priority' => fake()->randomElement(['Low', 'Medium', 'High']),
            'category' => fake()->randomElement(['Backend', 'UI', 'Bug', 'Database', 'DevOps']),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'order' => fake()->numberBetween(1, 10),
            'assigned_to' => null,
            'pic_name' => null,
        ];
    }
}
