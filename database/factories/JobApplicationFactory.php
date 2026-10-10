<?php

namespace Database\Factories;

use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companies = [
            'Tech Nusantara', 'Tokopedia', 'Gojek (GoTo)', 'Traveloka', 'Bukalapak',
            'Shopee Indonesia', 'Bank Central Asia (BCA)', 'Bank Mandiri', 'Telkomsel', 'Tiket.com',
            'Blibli', 'Ajaib', 'Bibit', 'Kargo Technologies', 'Xendit',
            'Midtrans', 'Sirclo', 'Pluang', 'DANA Indonesia', 'Glints',
        ];

        $positions = [
            'Senior Laravel Developer', 'Fullstack Engineer (PHP/Vue)', 'Backend Engineer (Laravel/Go)',
            'Lead Software Engineer', 'Frontend Developer (Livewire/Tailwind)', 'DevOps Engineer',
            'Product Engineer', 'Engineering Manager', 'Tech Lead', 'Solution Architect',
            'PHP Backend Specialist', 'Remote Software Consultant',
        ];

        $contractStatuses = ['contract', 'full_time', 'part_time', 'freelance', 'internship'];
        $applyStatuses = ['applied', 'hr_review', 'interview', 'interview_user', 'offering_letter', 'accepted', 'rejected'];
        $workplaceTypes = ['remote', 'hybrid', 'onsite'];
        $salaries = [
            'Rp 12.000.000 - Rp 18.000.000',
            'Rp 18.000.000 - Rp 25.000.000',
            'Rp 25.000.000 - Rp 35.000.000',
            'Rp 35.000.000 - Rp 50.000.000',
            '$2,500 - $4,000 / month',
            'Competitive (Negotiable)',
        ];
        $locations = [
            'Jakarta Selatan, Indonesia', 'Jakarta Pusat, Indonesia', 'BSD, Tangerang Selatan',
            'Bandung, Jawa Barat', 'Yogyakarta, Indonesia', 'Bali (Remote)', 'Singapore (Remote)',
        ];
        $benefitsList = [
            "BPJS Kesehatan & Ketenagakerjaan\nAsuransi Rawat Inap Swasta\nMacBook Pro M-Series\nAnnual Bonus (THR + Performance)\nFlexible Working Hours",
            "100% Work from Anywhere\nLearning & Certification Budget ($1000/yr)\nWellness Allowance\nStock Options / ESOP\nUnlimited Paid Time Off",
            "Health & Dental Insurance\nMeal Allowance\nInternet & Co-working Stipend\nParental Leave\nGym Membership",
        ];

        return [
            'user_id' => User::factory(),
            'company_name' => fake()->randomElement($companies),
            'position' => fake()->randomElement($positions),
            'contract_status' => fake()->randomElement($contractStatuses),
            'apply_status' => fake()->randomElement($applyStatuses),
            'salary' => fake()->randomElement($salaries),
            'location' => fake()->randomElement($locations),
            'workplace_type' => fake()->randomElement($workplaceTypes),
            'job_url' => fake()->url(),
            'benefits' => fake()->randomElement($benefitsList),
            'notes' => fake()->sentence(12),
            'applied_at' => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
