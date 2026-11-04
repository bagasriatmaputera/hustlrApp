<?php

namespace Tests\Feature\Portfolio;

use App\Livewire\Portfolio\Experiences\Edit as ExperienceEdit;
use App\Livewire\Portfolio\Profile\Edit as ProfileEdit;
use App\Livewire\Portfolio\Projects\Create as ProjectCreate;
use App\Livewire\Portfolio\Projects\Edit as ProjectEdit;
use App\Models\Portfolio\PortfolioExperience;
use App\Models\Portfolio\PortfolioJobDesc;
use App\Models\Portfolio\PortfolioProfile;
use App\Models\Portfolio\PortfolioProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_project_create_can_upload_files_to_s3(): void
    {
        Storage::fake('s3');
        config(['filesystems.default' => 's3']);

        $user = $this->authorizedUser();
        $this->actingAs($user);

        $coverFile = UploadedFile::fake()->image('cover.jpg');
        $galleryFile = UploadedFile::fake()->image('gallery1.jpg');

        Livewire::test(ProjectCreate::class)
            ->set('title_en', 'S3 Upload App')
            ->set('title_id', 'Aplikasi Upload S3')
            ->set('description_en', 'App with S3 storage')
            ->set('description_id', 'Aplikasi dengan penyimpanan S3')
            ->set('tech_stack_raw', 'Laravel, AWS S3')
            ->set('image_file', $coverFile)
            ->call('addMediaGalleryItem')
            ->set('media_gallery.0.file', $galleryFile)
            ->set('media_gallery.0.caption_en', 'S3 Gallery Preview')
            ->set('media_gallery.0.caption_id', 'Preview Galeri S3')
            ->call('save')
            ->assertRedirect(route('portfolio.projects.index'));

        $project = PortfolioProject::where('title->en', 'S3 Upload App')->firstOrFail();

        $this->assertNotNull($project->image);
        Storage::disk('s3')->assertExists($project->image);

        $this->assertCount(1, $project->media_gallery);
        $galleryPath = $project->media_gallery[0]['image'];
        Storage::disk('s3')->assertExists($galleryPath);
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

    public function test_portfolio_profile_edit_can_upload_resume_and_update_socials(): void
    {
        Storage::fake('s3');
        config(['filesystems.default' => 's3']);

        $user = $this->authorizedUser();
        $this->actingAs($user);

        $profile = PortfolioProfile::create([
            'name' => 'Bagas Test',
            'job_position' => 'Software Engineer',
            'summary' => 'Bio test',
            'location' => 'Jakarta, Indonesia',
            'available_for_work' => true,
            'socials' => [
                'email' => 'bagas@example.com',
                'github' => 'https://github.com/bagas',
                'linkedin' => 'https://linkedin.com/in/bagas',
                'instagram' => 'https://instagram.com/bgs.rptr',
                'tiktok' => 'https://tiktok.com/@bagasriatmaputra',
            ],
        ]);

        $resumeFile = UploadedFile::fake()->create('curriculum-vitae.pdf', 500, 'application/pdf');

        Livewire::test(ProfileEdit::class)
            ->assertSet('name', 'Bagas Test')
            ->assertSet('socials.instagram', 'https://instagram.com/bgs.rptr')
            ->assertSet('socials.tiktok', 'https://tiktok.com/@bagasriatmaputra')
            ->set('resume_file', $resumeFile)
            ->call('save')
            ->assertHasNoErrors();

        $profile->refresh();
        $this->assertNotNull($profile->resume_url);
        $this->assertStringStartsWith('portfolio/resume/', $profile->resume_url);
        Storage::disk('s3')->assertExists($profile->resume_url);
    }

    public function test_portfolio_profile_edit_can_remove_resume(): void
    {
        Storage::fake('s3');
        config(['filesystems.default' => 's3']);

        $user = $this->authorizedUser();
        $this->actingAs($user);

        Storage::disk('s3')->put('portfolio/resume/test-cv.pdf', 'dummy content');

        $profile = PortfolioProfile::create([
            'name' => 'Bagas Test',
            'job_position' => 'Software Engineer',
            'summary' => 'Bio test',
            'location' => 'Jakarta, Indonesia',
            'resume_url' => 'portfolio/resume/test-cv.pdf',
            'available_for_work' => true,
            'socials' => [],
        ]);

        Livewire::test(ProfileEdit::class)
            ->call('removeResume')
            ->assertHasNoErrors();

        $profile->refresh();
        $this->assertNull($profile->resume_url);
        Storage::disk('s3')->assertMissing('portfolio/resume/test-cv.pdf');
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
