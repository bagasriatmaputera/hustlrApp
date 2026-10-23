<?php

namespace Tests\Feature\Portfolio;

use App\Livewire\Portfolio\Experiences\Edit as ExperienceEdit;
use App\Livewire\Portfolio\Projects\Edit as ProjectEdit;
use App\Models\Portfolio\PortfolioExperience;
use App\Models\Portfolio\PortfolioJobDesc;
use App\Models\Portfolio\PortfolioProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PortfolioEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_edit_page_loads_existing_data(): void
    {
        $user = $this->authorizedUser();
        $project = $this->createProject();

        $this->actingAs($user)
            ->get(route('portfolio.projects.edit', $project->project_id))
            ->assertOk()
            ->assertSee('Legacy Project')
            ->assertSee('Proyek Lama')
            ->assertSee('Legacy project description');

        Livewire::test(ProjectEdit::class, ['portfolioProject' => $project])
            ->assertSet('title_en', 'Legacy Project')
            ->assertSet('title_id', 'Proyek Lama')
            ->assertSet('description_en', 'Legacy project description')
            ->assertSet('description_id', 'Deskripsi proyek lama')
            ->assertSet('tech_stack_raw', 'Laravel, Vue.js')
            ->assertSet('image', '/images/projects/legacy.jpg')
            ->assertSet('featured', true)
            ->assertSet('order', 3);
    }

    public function test_experience_edit_page_loads_existing_data(): void
    {
        $user = $this->authorizedUser();
        $experience = $this->createExperience();

        $this->actingAs($user)
            ->get(route('portfolio.experiences.edit', $experience->experience_id))
            ->assertOk()
            ->assertSee('Legacy Company')
            ->assertSee('Senior Engineer')
            ->assertSee('Led the legacy migration');

        Livewire::test(ExperienceEdit::class, ['portfolioExperience' => $experience])
            ->assertSet('company_name', 'Legacy Company')
            ->assertSet('position', 'Senior Engineer')
            ->assertSet('start_date', '2020-01-15')
            ->assertSet('end_date', '2023-06-30')
            ->assertSet('location', 'Jakarta')
            ->assertSet('workplace_type', 'Hybrid')
            ->assertSet('contract_type', 'Full-time')
            ->assertSet('job_descs.0.description', 'Led the legacy migration');
    }

    private function createProject(): PortfolioProject
    {
        return PortfolioProject::create([
            'title' => ['id' => 'Proyek Lama', 'en' => 'Legacy Project'],
            'description' => ['id' => 'Deskripsi proyek lama', 'en' => 'Legacy project description'],
            'image' => '/images/projects/legacy.jpg',
            'gif_image' => null,
            'tech_stack' => ['Laravel', 'Vue.js'],
            'live_url' => 'https://example.com',
            'repo_url' => 'https://github.com/example/legacy',
            'featured' => true,
            'is_live' => true,
            'is_repo' => true,
            'order' => 3,
        ]);
    }

    private function createExperience(): PortfolioExperience
    {
        $experience = PortfolioExperience::create([
            'company_name' => 'Legacy Company',
            'position' => 'Senior Engineer',
            'start_date' => '2020-01-15',
            'end_date' => '2023-06-30',
            'location' => 'Jakarta',
            'workplace_type' => 'Hybrid',
            'contract_type' => 'Full-time',
            'order' => 2,
        ]);

        PortfolioJobDesc::create([
            'experience_id' => $experience->experience_id,
            'description' => 'Led the legacy migration',
            'learning_outcomes' => null,
        ]);

        return $experience;
    }
}
