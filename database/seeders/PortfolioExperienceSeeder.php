<?php

namespace Database\Seeders;

use App\Models\Portfolio\PortfolioExperience;
use App\Models\Portfolio\PortfolioJobDesc;
use Illuminate\Database\Seeder;

class PortfolioExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $experiences = [
            [
                'company_name' => 'The One Retail',
                'position' => 'Software Engineer',
                'start_date' => '2026-01-01',
                'end_date' => null,
                'location' => 'South Jakarta, Jakarta, Indonesia',
                'workplace_type' => 'On-site',
                'contract_type' => 'Full-time',
                'order' => 1,
                'job_descs' => [
                    'Developed web and backend features using Laravel, TypeScript, Vue.js, and AngularJS',
                    'Implemented Queue Management and Continuous Integration & Continuous Delivery (CI/CD) pipelines',
                    'Supported multi-platform mobile application development with React Native and Ionic Framework',
                ],
            ],
            [
                'company_name' => 'PT. DigiCraft Syakeera Ventures',
                'position' => 'Backend Developer (Internship)',
                'start_date' => '2025-09-01',
                'end_date' => '2025-12-31',
                'location' => 'Surakarta, Central Java, Indonesia',
                'workplace_type' => 'Remote',
                'contract_type' => 'Internship',
                'order' => 2,
                'job_descs' => [
                    'Built high-performance RESTful APIs using PHP and Laravel framework',
                    'Designed relational database structures and optimized database queries',
                    'Actively participated in daily standups, sprint planning, and code reviews based on Agile/Scrum',
                ],
            ],
            [
                'company_name' => 'Eduwork',
                'position' => 'Fullstack Web Developer - Trainee',
                'start_date' => '2024-12-01',
                'end_date' => '2025-05-31',
                'location' => 'Remote',
                'workplace_type' => 'Remote',
                'contract_type' => 'Training',
                'order' => 3,
                'job_descs' => [
                    'Developed fullstack web applications using PHP, Laravel, MySQL, and Vue.js',
                    'Conducted API endpoint testing and documentation using Postman',
                    'Applied Model-View-Controller (MVC) architecture standards, Git version control, and responsive styling with Bootstrap & CSS',
                ],
            ],
            [
                'company_name' => 'Yamaha Music Product Asia',
                'position' => 'Operator',
                'start_date' => '2022-12-01',
                'end_date' => '2025-01-31',
                'location' => 'West Cikarang, West Java, Indonesia',
                'workplace_type' => 'On-site',
                'contract_type' => 'Full-time',
                'order' => 4,
                'job_descs' => [
                    'Collaborated effectively within teams to consistently achieve manufacturing production targets',
                    'Managed operational data tracking and record-keeping using Microsoft Excel',
                ],
            ],
        ];

        foreach ($experiences as $data) {
            $jobDescs = $data['job_descs'];
            unset($data['job_descs']);

            $experience = PortfolioExperience::firstOrCreate(
                [
                    'company_name' => $data['company_name'],
                    'position' => $data['position'],
                ],
                $data
            );

            foreach ($jobDescs as $description) {
                PortfolioJobDesc::firstOrCreate(
                    [
                        'experience_id' => $experience->experience_id,
                        'description' => $description,
                    ],
                    [
                        'learning_outcomes' => null,
                    ]
                );
            }
        }

        $this->command->info('Portfolio experiences seeded successfully.');
    }
}
