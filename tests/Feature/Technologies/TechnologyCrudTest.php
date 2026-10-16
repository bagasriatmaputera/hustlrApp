<?php

namespace Tests\Feature\Technologies;

use App\Livewire\Technologies\Index;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Technology;
use Database\Seeders\TechnologySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TechnologyCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_technologies_page(): void
    {
        $response = $this->get(route('technologies.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_technology_seeder_populates_comprehensive_technologies(): void
    {
        $this->seed(TechnologySeeder::class);

        $this->assertDatabaseHas('technologies', [
            'tech_name' => 'Laravel',
            'category' => 'Backend',
        ]);

        $this->assertDatabaseHas('technologies', [
            'tech_name' => 'React.js',
            'category' => 'Frontend',
        ]);

        $this->assertDatabaseHas('technologies', [
            'tech_name' => 'Next.js',
            'category' => 'Frontend',
        ]);

        $this->assertDatabaseHas('technologies', [
            'tech_name' => 'Vue.js',
            'category' => 'Frontend',
        ]);

        $this->assertDatabaseHas('technologies', [
            'tech_name' => 'Angular',
            'category' => 'Frontend',
        ]);

        $this->assertDatabaseHas('technologies', [
            'tech_name' => 'Livewire',
            'category' => 'Frontend',
        ]);

        $this->assertDatabaseHas('technologies', [
            'tech_name' => 'MySQL',
            'category' => 'Database',
        ]);

        $this->assertDatabaseHas('technologies', [
            'tech_name' => 'Golang',
            'category' => 'Backend',
        ]);

        $this->assertDatabaseHas('technologies', [
            'tech_name' => 'Docker',
            'category' => 'DevOps & Cloud',
        ]);

        $this->assertDatabaseHas('technologies', [
            'tech_name' => 'Flutter',
            'category' => 'Mobile',
        ]);

        $this->assertGreaterThan(50, Technology::count());
    }

    public function test_user_can_view_technologies_index(): void
    {
        $user = $this->authorizedUser();
        Technology::create(['tech_name' => 'Laravel', 'category' => 'Backend']);
        Technology::create(['tech_name' => 'Vue.js', 'category' => 'Frontend']);

        $this->actingAs($user);

        $response = $this->get(route('technologies.index'));
        $response->assertOk();
        $response->assertSee('Technologies & Tech Stack');
        $response->assertSee('Laravel');
        $response->assertSee('Vue.js');
    }

    public function test_user_can_search_and_filter_technologies(): void
    {
        $user = $this->authorizedUser();
        Technology::create(['tech_name' => 'FastAPI', 'category' => 'Backend']);
        Technology::create(['tech_name' => 'SvelteKit', 'category' => 'Frontend']);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->set('search', 'FastAPI')
            ->assertSee('FastAPI')
            ->assertDontSee('SvelteKit');

        Livewire::test(Index::class)
            ->set('categoryFilter', 'Frontend')
            ->assertSee('SvelteKit')
            ->assertDontSee('FastAPI');
    }

    public function test_user_can_create_technology(): void
    {
        $user = $this->authorizedUser();
        $this->actingAs($user);

        Livewire::test(Index::class)
            ->call('openCreateModal')
            ->set('techName', 'Kubernetes')
            ->set('category', 'DevOps & Cloud')
            ->call('createTechnology')
            ->assertHasNoErrors()
            ->assertSee('Kubernetes');

        $this->assertDatabaseHas('technologies', [
            'tech_name' => 'Kubernetes',
            'category' => 'DevOps & Cloud',
        ]);
    }

    public function test_technology_name_must_be_unique(): void
    {
        $user = $this->authorizedUser();
        Technology::create(['tech_name' => 'Django', 'category' => 'Backend']);
        $this->actingAs($user);

        Livewire::test(Index::class)
            ->set('techName', 'Django')
            ->set('category', 'Backend')
            ->call('createTechnology')
            ->assertHasErrors(['techName']);
    }

    public function test_user_can_update_technology(): void
    {
        $user = $this->authorizedUser();
        $tech = Technology::create(['tech_name' => 'ReactJS', 'category' => 'Frontend']);
        $this->actingAs($user);

        Livewire::test(Index::class)
            ->call('openEditModal', $tech->tech_id)
            ->set('techName', 'React.js')
            ->set('category', 'Frontend')
            ->call('updateTechnology')
            ->assertHasNoErrors()
            ->assertSee('React.js');

        $this->assertDatabaseHas('technologies', [
            'tech_id' => $tech->tech_id,
            'tech_name' => 'React.js',
        ]);
    }

    public function test_user_can_delete_technology(): void
    {
        $user = $this->authorizedUser();
        $tech = Technology::create(['tech_name' => 'Legacy Tool', 'category' => 'Backend']);
        $this->actingAs($user);

        Livewire::test(Index::class)
            ->call('confirmDelete', $tech->tech_id)
            ->call('deleteTechnology')
            ->assertHasNoErrors();

        $this->assertSoftDeleted('technologies', [
            'tech_id' => $tech->tech_id,
        ]);
    }

    public function test_delete_modal_shows_associated_projects_count(): void
    {
        $user = $this->authorizedUser();
        $status = ProjectStatus::create(['status_name' => 'In Progress']);
        $tech = Technology::create(['tech_name' => 'PostgreSQL', 'category' => 'Database']);

        $project = Project::create([
            'project_name' => 'Postgres App',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'created_by' => $user->id,
        ]);

        $project->technologies()->attach($tech->tech_id);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->call('confirmDelete', $tech->tech_id)
            ->assertSet('deleteProjectsCount', 1)
            ->assertSee('Warning: Associated Projects');
    }
}
