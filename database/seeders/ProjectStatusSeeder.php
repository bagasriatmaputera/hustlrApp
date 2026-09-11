<?php

namespace Database\Seeders;

use App\Models\ProjectStatus;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    /**
     * @var array<int, string>
     */
    private array $statuses = [
        'Planning',
        'In Progress',
        'On Hold',
        'Completed',
        'Cancelled',
    ];

    public function run(): void
    {
        foreach ($this->statuses as $status) {
            ProjectStatus::firstOrCreate(['status_name' => $status]);
        }
    }
}
