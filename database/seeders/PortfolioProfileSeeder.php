<?php

namespace Database\Seeders;

use App\Models\Portfolio\PortfolioProfile;
use Illuminate\Database\Seeder;

class PortfolioProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profile = PortfolioProfile::query()->oldest('created_at')->first();

        PortfolioProfile::updateOrCreate(
            ['profile_id' => $profile?->profile_id],
            [
                'name' => 'Bagas Riatma Putera',
                'job_position' => 'Software Engineer',
                'summary' => 'Software Engineer at The One Retail focused on building modern web applications, RESTful APIs, and scalable systems using Laravel, TypeScript, Vue.js, and CI/CD.',
                'location' => 'Jakarta, Indonesia',
                'resume_url' => '#',
                'available_for_work' => true,
                'socials' => [
                    'github' => 'https://github.com/bagasriatmaputera',
                    'linkedin' => 'https://www.linkedin.com/in/bagasriatmap/',
                    'email' => 'bagasriatmap@gmail.com',
                ],
            ]
        );

        $this->command->info('Portfolio profile seeded successfully.');
    }
}
