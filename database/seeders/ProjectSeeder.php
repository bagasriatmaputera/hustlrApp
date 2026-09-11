<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ProjectStatus::all();
        $owner = User::first();
        $clients = Client::factory(5)->create();

        $technologies = collect([
            ['tech_name' => 'Laravel', 'category' => 'Backend'],
            ['tech_name' => 'React', 'category' => 'Frontend'],
            ['tech_name' => 'Vue.js', 'category' => 'Frontend'],
            ['tech_name' => 'Tailwind CSS', 'category' => 'Frontend'],
            ['tech_name' => 'MySQL', 'category' => 'Database'],
            ['tech_name' => 'PostgreSQL', 'category' => 'Database'],
            ['tech_name' => 'Docker', 'category' => 'DevOps'],
            ['tech_name' => 'Flutter', 'category' => 'Mobile'],
            ['tech_name' => 'Redis', 'category' => 'Database'],
            ['tech_name' => 'Alpine.js', 'category' => 'Frontend'],
        ])->map(fn (array $tech) => Technology::firstOrCreate(
            ['tech_name' => $tech['tech_name']],
            ['category' => $tech['category']]
        ));

        $projects = [
            [
                'project_name' => 'E-Commerce Platform',
                'description' => 'Full-featured e-commerce platform with inventory management, payment processing, and customer analytics dashboard.',
                'ownership_type' => 'Client',
                'link_testing' => 'https://staging.ecommerce-demo.test',
                'link_prod' => 'https://ecommerce-demo.com',
            ],
            [
                'project_name' => 'Internal HR System',
                'description' => 'Employee management system with leave tracking, performance reviews, and payroll integration.',
                'ownership_type' => 'Internal',
                'link_testing' => 'https://hr.staging.internal.test',
                'link_prod' => null,
            ],
            [
                'project_name' => 'Mobile Banking App',
                'description' => 'Cross-platform mobile banking application with real-time transaction tracking and biometric authentication.',
                'ownership_type' => 'Client',
                'link_testing' => 'https://banking-app.staging.test',
                'link_prod' => 'https://banking-app.com',
            ],
            [
                'project_name' => 'Portfolio Website',
                'description' => 'Personal portfolio website with blog, project showcase, and contact form.',
                'ownership_type' => 'Freelance',
                'link_testing' => null,
                'link_prod' => 'https://portfolio.example.com',
            ],
            [
                'project_name' => 'Learning Management System',
                'description' => 'Online learning platform with course management, quizzes, progress tracking, and certificate generation.',
                'ownership_type' => 'Internal',
                'link_testing' => 'https://lms.staging.test',
                'link_prod' => null,
            ],
            [
                'project_name' => 'Restaurant POS System',
                'description' => 'Point of sale system for restaurants with table management, order tracking, and kitchen display.',
                'ownership_type' => 'Client',
                'link_testing' => null,
                'link_prod' => null,
            ],
            [
                'project_name' => 'CRM Dashboard',
                'description' => 'Customer relationship management dashboard with sales pipeline, contact management, and reporting.',
                'ownership_type' => 'Freelance',
                'link_testing' => 'https://crm.staging.test',
                'link_prod' => 'https://crm-dashboard.com',
            ],
            [
                'project_name' => 'IoT Monitoring Platform',
                'description' => 'Real-time IoT device monitoring platform with sensor data visualization and alert system.',
                'ownership_type' => 'Client',
                'link_testing' => 'https://iot.staging.test',
                'link_prod' => null,
            ],
        ];

        foreach ($projects as $index => $projectData) {
            $project = Project::create([
                ...$projectData,
                'status_id' => $statuses->random()->status_id,
                'client_id' => $projectData['ownership_type'] === 'Client'
                    ? $clients->random()->client_id
                    : null,
                'created_by' => $owner->id,
                'start_date' => now()->subMonths(rand(1, 8))->subDays(rand(0, 30)),
                'end_date' => rand(0, 1)
                    ? now()->addMonths(rand(1, 6))
                    : null,
            ]);

            $project->technologies()->attach(
                $technologies->random(rand(2, 5))->pluck('tech_id')
            );

            $project->members()->attach($owner->id, [
                'role_in_project' => 'Owner',
                'joined_at' => $project->created_at,
            ]);
        }
    }
}
