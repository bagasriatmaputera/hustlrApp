<?php

namespace Database\Factories;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $technologies = [
            'Frontend' => ['React', 'Vue.js', 'Angular', 'Svelte', 'Next.js', 'Nuxt.js', 'Tailwind CSS', 'Alpine.js'],
            'Backend' => ['Laravel', 'Django', 'Express.js', 'Spring Boot', 'FastAPI', 'NestJS', 'Ruby on Rails'],
            'Database' => ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'MariaDB'],
            'DevOps' => ['Docker', 'Kubernetes', 'AWS', 'GCP', 'Nginx', 'GitHub Actions', 'CI/CD'],
            'Mobile' => ['Flutter', 'React Native', 'Swift', 'Kotlin'],
        ];

        $category = fake()->randomElement(array_keys($technologies));

        return [
            'tech_name' => fake()->randomElement($technologies[$category]),
            'category' => $category,
        ];
    }
}
