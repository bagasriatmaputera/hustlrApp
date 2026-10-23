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
                'description' => [
                    'id' => 'Mengembangkan dan mengelola arsitektur aplikasi ritel, integrasi queue management, serta penerapan pipeline CI/CD untuk memastikan pengiriman fitur yang andal dan terukur.',
                    'en' => 'Developing and managing retail application architecture, queue management integration, and CI/CD pipelines to ensure reliable and scalable feature delivery.',
                ],
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
                'description' => [
                    'id' => 'Fokus pada pengembangan API backend yang andal, pemodelan database relasional, serta kolaborasi tim pengembang dengan metodologi Scrum.',
                    'en' => 'Focused on developing robust backend APIs, relational database modeling, and developer team collaboration using Scrum methodology.',
                ],
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
                'description' => [
                    'id' => 'Program intensif fullstack web development mencakup perancangan basis data, arsitektur MVC, integrasi API, dan pembuatan antarmuka responsif.',
                    'en' => 'Intensive fullstack web development program covering database design, MVC architecture, API integration, and responsive UI creation.',
                ],
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
                'description' => [
                    'id' => 'Menjalankan proses operasional produksi dengan standar kualitas tinggi, koordinasi tim kerja, serta dokumentasi administrasi berkala.',
                    'en' => 'Managed production operational processes with high quality standards, team coordination, and periodic administrative documentation.',
                ],
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

            $experience = PortfolioExperience::query()
                ->where('order', $data['order'])
                ->first()
                ?? PortfolioExperience::query()
                    ->where('company_name', $data['company_name'])
                    ->where('position', $data['position'])
                    ->first()
                ?? new PortfolioExperience;

            $experience->fill($data)->save();

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
