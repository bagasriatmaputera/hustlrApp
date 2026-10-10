<?php

namespace Database\Seeders;

use App\Models\Portfolio\PortfolioProfile;
use Illuminate\Database\Seeder;

class PortfolioProfileSeeder extends Seeder
{
    public function run(): void
    {
        PortfolioProfile::firstOrCreate(
            ['name' => 'Bagas Riatmaputera'],
            [
                'job_position' => 'Software Engineer',
                'summary' => 'Software Engineer with experience in building web and mobile applications. Skilled in Laravel, Vue.js, React, and modern backend technologies. Passionate about delivering high-quality, scalable solutions.',
                'image_path' => null,
            ]
        );

        $this->command->info('Portfolio profile seeded successfully.');
    }
}
