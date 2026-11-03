<?php

namespace Tests\Feature\Portfolio;

use App\Livewire\Portfolio\Experiences\Edit as ExperienceEdit;
use App\Livewire\Portfolio\Projects\Create as ProjectCreate;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

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
            ->assertSet('problem_solution_en', 'Legacy Problem & Solution EN')
            ->assertSet('problem_solution_id', 'Masalah & Solusi ID')
            ->assertSet('challenges_learnings_en', 'Legacy Challenges & Learnings EN')
            ->assertSet('challenges_learnings_id', 'Tantangan & Pelajaran ID')
            ->assertSet('media_gallery.0.image', '/images/projects/gallery1.jpg')
            ->assertSet('media_gallery.0.caption_en', 'Gallery 1')
            ->assertSet('tech_stack_raw', 'Laravel, Vue.js')
            ->assertSet('image', '/images/projects/legacy.jpg')
            ->assertSet('featured', true)
            ->assertSet('order', 3);
    }

    public function test_project_create_can_save_detail_fields(): void
    {
        $user = $this->authorizedUser();

        $this->actingAs($user);

        Livewire::test(ProjectCreate::class)
            ->set('title_en', 'New AI App')
            ->set('title_id', 'Aplikasi AI Baru')
            ->set('description_en', 'AI App description')
            ->set('description_id', 'Deskripsi Aplikasi AI')
            ->set('tech_stack_raw', 'Laravel, Python, Tailwind')
            ->set('problem_solution_en', 'Solves manual report generation')
            ->set('problem_solution_id', 'Menyelesaikan pembuatan laporan manual')
            ->set('challenges_learnings_en', 'Prompt engineering and token limits')
            ->set('challenges_learnings_id', 'Optimasi prompt dan batasan token')
            ->call('addMediaGalleryItem')
            ->set('media_gallery.0.image', '/images/projects/ai-screen.png')
            ->set('media_gallery.0.caption_en', 'AI Dashboard')
            ->set('media_gallery.0.caption_id', 'Dasbor AI')
            ->call('save')
            ->assertRedirect(route('portfolio.projects.index'));

        $this->assertDatabaseHas('portfolio_projects', [
            'title->en' => 'New AI App',
            'title->id' => 'Aplikasi AI Baru',
            'problem_solution->en' => 'Solves manual report generation',
            'problem_solution->id' => 'Menyelesaikan pembuatan laporan manual',
            'challenges_learnings->en' => 'Prompt engineering and token limits',
            'challenges_learnings->id' => 'Optimasi prompt dan batasan token',
        ]);

        $project = PortfolioProject::where('title->en', 'New AI App')->firstOrFail();
        $this->assertCount(1, $project->media_gallery);
        $this->assertSame('/images/projects/ai-screen.png', $project->media_gallery[0]['image']);
        $this->assertSame('AI Dashboard', $project->media_gallery[0]['caption']['en']);
        $this->assertSame('Dasbor AI', $project->media_gallery[0]['caption']['id']);
    }

    public function test_project_edit_can_update_detail_fields_and_remove_media(): void
    {
        $user = $this->authorizedUser();
        $project = $this->createProject();

        $this->actingAs($user);

        Livewire::test(ProjectEdit::class, ['portfolioProject' => $project])
            ->set('problem_solution_en', 'Updated problem solution EN')
            ->set('problem_solution_id', 'Update masalah solusi ID')
            ->call('removeMediaGalleryItem', 0)
            ->call('save')
            ->assertRedirect(route('portfolio.projects.index'));

        $project->refresh();
        $this->assertSame('Updated problem solution EN', $project->problem_solution['en']);
        $this->assertSame('Update masalah solusi ID', $project->problem_solution['id']);
        $this->assertEmpty($project->media_gallery);
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
            'problem_solution' => ['id' => 'Masalah & Solusi ID', 'en' => 'Legacy Problem & Solution EN'],
            'challenges_learnings' => ['id' => 'Tantangan & Pelajaran ID', 'en' => 'Legacy Challenges & Learnings EN'],
            'media_gallery' => [
                [
                    'image' => '/images/projects/gallery1.jpg',
                    'caption' => ['id' => 'Galeri 1', 'en' => 'Gallery 1'],
                ],
            ],
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
