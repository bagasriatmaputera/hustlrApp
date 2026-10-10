<?php

namespace Database\Seeders;

use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'email' => 'owner@gmail.com',
            'name' => 'Owner',
            'full_name' => 'System Owner',
        ]);

        $samples = [
            [
                'company_name' => 'Tech Nusantara',
                'position' => 'Senior Fullstack Engineer',
                'contract_status' => 'full_time',
                'apply_status' => 'offering_letter',
                'salary' => 'Rp 28.000.000 - Rp 35.000.000',
                'location' => 'Jakarta Selatan, Indonesia',
                'workplace_type' => 'hybrid',
                'job_url' => 'https://careers.technusantara.id/jobs/senior-fullstack',
                'benefits' => "BPJS Kesehatan & Ketenagakerjaan\nPrivate Health Insurance (Full Family Coverage)\nMacBook Pro M3 Max\nLearning & Conference Budget ($1,500/year)\nPerformance Bonus (up to 3x monthly salary)",
                'notes' => 'Passed technical user interview with distinction. Offering letter received on Friday. Target start date next month.',
                'applied_at' => now()->subDays(18)->toDateString(),
            ],
            [
                'company_name' => 'Fintech Go Indonesia',
                'position' => 'Lead Laravel Architect',
                'contract_status' => 'full_time',
                'apply_status' => 'interview_user',
                'salary' => 'Rp 32.000.000 - Rp 42.000.000',
                'location' => 'Jakarta Pusat, Indonesia',
                'workplace_type' => 'remote',
                'job_url' => 'https://fintechgo.com/careers/lead-architect',
                'benefits' => "100% Remote / Work from anywhere\nWellness allowance\nDental & Optical benefits\nAnnual Company Retreat in Bali\nStock Options (ESOP)",
                'notes' => 'Completed live coding session. Next round is technical interview with VP of Engineering on Wednesday at 2 PM.',
                'applied_at' => now()->subDays(12)->toDateString(),
            ],
            [
                'company_name' => 'CloudScale Asia',
                'position' => 'DevOps & Backend Specialist',
                'contract_status' => 'contract',
                'apply_status' => 'interview',
                'salary' => '$3,500 - $4,500 / month',
                'location' => 'Singapore (Remote)',
                'workplace_type' => 'remote',
                'job_url' => 'https://cloudscale.asia/jobs/devops-backend',
                'benefits' => "USD-denominated compensation\nFlexible work schedule\nEquipment budget ($2,000)\nCoworking space membership reimbursement",
                'notes' => 'HR initial screening went well. Discussed Kubernetes & high-availability Laravel architecture.',
                'applied_at' => now()->subDays(8)->toDateString(),
            ],
            [
                'company_name' => 'Digital Kreatif Studio',
                'position' => 'Part-time Livewire & Alpine Consultant',
                'contract_status' => 'part_time',
                'apply_status' => 'hr_review',
                'salary' => 'Rp 10.000.000 - Rp 14.000.000',
                'location' => 'Bandung, Jawa Barat',
                'workplace_type' => 'remote',
                'job_url' => 'https://digitalkreatif.id/careers',
                'benefits' => "Flexible 20 hours/week\nProject milestone completion bonuses\nMentorship program",
                'notes' => 'Submitted CV and portfolio link of Hustlr dashboard and IPS projects.',
                'applied_at' => now()->subDays(4)->toDateString(),
            ],
            [
                'company_name' => 'Global Logistics Tech',
                'position' => 'Backend Engineer (PHP 8.4)',
                'contract_status' => 'full_time',
                'apply_status' => 'applied',
                'salary' => 'Rp 20.000.000 - Rp 26.000.000',
                'location' => 'BSD City, Tangerang',
                'workplace_type' => 'hybrid',
                'job_url' => 'https://globallogistics.co.id/jobs',
                'benefits' => "BPJS + Private Insurance\nCatering lunch provided\nTransport allowance",
                'notes' => 'Application submitted through LinkedIn Easy Apply.',
                'applied_at' => now()->subDays(2)->toDateString(),
            ],
            [
                'company_name' => 'Media Nusantara Tech',
                'position' => 'Frontend Engineer (Livewire / Tailwind)',
                'contract_status' => 'full_time',
                'apply_status' => 'rejected',
                'salary' => 'Rp 16.000.000 - Rp 22.000.000',
                'location' => 'Jakarta Barat, Indonesia',
                'workplace_type' => 'onsite',
                'job_url' => 'https://medianusantara.com/careers',
                'benefits' => 'BPJS, Annual Bonus, Health Insurance',
                'notes' => 'Position closed internally. Retaining contact for future openings.',
                'applied_at' => now()->subDays(25)->toDateString(),
            ],
            [
                'company_name' => 'InnoPay Global',
                'position' => 'Senior API Engineer',
                'contract_status' => 'contract',
                'apply_status' => 'accepted',
                'salary' => '$4,000 / month',
                'location' => 'Remote (Global)',
                'workplace_type' => 'remote',
                'job_url' => 'https://innopay.io/careers',
                'benefits' => 'Unlimited PTO, Wellness Stipend, 401(k) match equivalent',
                'notes' => 'Accepted offer. Starting next month on the payments squad.',
                'applied_at' => now()->subDays(30)->toDateString(),
            ],
        ];

        foreach ($samples as $sample) {
            JobApplication::create(array_merge($sample, [
                'user_id' => $user->id,
            ]));
        }

        // Generate 15 additional randomized records to demonstrate infinite scroll
        JobApplication::factory()->count(15)->create([
            'user_id' => $user->id,
        ]);
    }
}
