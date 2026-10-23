<?php

namespace Tests\Feature\Api;

use App\Models\Portfolio\PortfolioExperience;
use App\Models\Portfolio\PortfolioJobDesc;
use App\Models\Portfolio\PortfolioProfile;
use App\Models\Portfolio\PortfolioProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortfolioApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_portfolio_endpoint_returns_all_sections(): void
    {
        $profile = PortfolioProfile::create([
            'name' => 'Bagas',
            'job_position' => 'Backend Engineer',
            'summary' => 'Builds reliable systems.',
            'image_path' => 'portfolio/profile/avatar.webp',
            'location' => 'Jakarta',
            'resume_url' => 'https://example.com/resume.pdf',
            'available_for_work' => true,
            'socials' => [
                'github' => 'https://github.com/example',
                'linkedin' => 'https://linkedin.com/in/example',
                'email' => 'example@example.com',
            ],
        ]);

        PortfolioProject::create([
            'title' => ['id' => 'Portfolio Saya', 'en' => 'Portfolio Me'],
            'description' => ['id' => 'Proyek portfolio', 'en' => 'Portfolio project'],
            'image' => 'https://cdn.example.com/project.webp',
            'gif_image' => 'portfolio/projects/demo.gif',
            'tech_stack' => ['Laravel', 'Livewire'],
            'live_url' => 'https://example.com',
            'repo_url' => 'https://github.com/example/portfolio',
            'featured' => true,
            'is_live' => true,
            'is_repo' => true,
            'order' => 1,
        ]);

        $experience = PortfolioExperience::create([
            'company_name' => 'Hustlr',
            'position' => 'Backend Engineer',
            'description' => [
                'id' => 'Membangun API portfolio',
                'en' => 'Built portfolio API',
            ],
            'start_date' => '2024-01-01',
            'end_date' => null,
            'location' => 'Jakarta',
            'workplace_type' => 'Remote',
            'contract_type' => 'Full-time',
            'order' => 1,
        ]);

        PortfolioJobDesc::create([
            'experience_id' => $experience->experience_id,
            'description' => 'Built portfolio API',
            'learning_outcomes' => null,
        ]);

        $this->getJson('/api/portfolio')
            ->assertOk()
            ->assertJsonPath('data.profile.name', 'Bagas')
            ->assertJsonPath('data.profile.location', 'Jakarta')
            ->assertJsonPath('data.profile.resume_url', 'https://example.com/resume.pdf')
            ->assertJsonPath('data.profile.available_for_work', true)
            ->assertJsonPath('data.profile.socials.github', 'https://github.com/example')
            ->assertJsonPath('data.profile.image_url', Storage::disk('public')->url($profile->image_path))
            ->assertJsonPath('data.projects.0.title.en', 'Portfolio Me')
            ->assertJsonPath('data.projects.0.image_url', 'https://cdn.example.com/project.webp')
            ->assertJsonPath('data.projects.0.gif_image_url', Storage::disk('public')->url('portfolio/projects/demo.gif'))
            ->assertJsonPath('data.projects.0.featured', true)
            ->assertJsonPath('data.experiences.0.is_current', true)
            ->assertJsonPath('data.experiences.0.description.id', 'Membangun API portfolio')
            ->assertJsonPath('data.experiences.0.description.en', 'Built portfolio API')
            ->assertJsonPath('data.experiences.0.job_descs.0.description', 'Built portfolio API');
    }

    public function test_portfolio_endpoint_returns_null_profile_when_profile_is_missing(): void
    {
        $this->getJson('/api/portfolio')
            ->assertOk()
            ->assertJsonPath('data.profile', null)
            ->assertJsonCount(0, 'data.projects')
            ->assertJsonCount(0, 'data.experiences');
    }

    public function test_projects_endpoint_filters_featured_projects(): void
    {
        PortfolioProject::create($this->projectData('Featured Project', true));
        PortfolioProject::create($this->projectData('Regular Project', false));

        $this->getJson('/api/portfolio/projects?featured=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title.en', 'Featured Project');
    }

    /**
     * @return array<string, mixed>
     */
    private function projectData(string $title, bool $featured): array
    {
        return [
            'title' => ['id' => $title, 'en' => $title],
            'description' => ['id' => $title, 'en' => $title],
            'image' => null,
            'gif_image' => null,
            'tech_stack' => ['Laravel'],
            'live_url' => null,
            'repo_url' => null,
            'featured' => $featured,
            'is_live' => false,
            'is_repo' => false,
            'order' => 1,
        ];
    }
}
