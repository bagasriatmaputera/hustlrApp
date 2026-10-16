<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('projects.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_projects_page(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->get(route('projects.index'));

        $response->assertOk();
    }

    public function test_projects_page_displays_projects(): void
    {
        $user = $this->authorizedUser();
        $status = ProjectStatus::create(['status_name' => 'In Progress']);

        Project::create([
            'project_name' => 'Test Project Alpha',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('projects.index'));

        $response->assertOk();
        $response->assertSee('Test Project Alpha');
    }

    public function test_projects_page_shows_empty_state_when_no_projects(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->get(route('projects.index'));

        $response->assertOk();
        $response->assertSee('No projects yet');
    }

    public function test_guests_cannot_access_create_project_page(): void
    {
        $response = $this->get(route('projects.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_create_project_page(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->get(route('projects.create'));

        $response->assertOk();
    }
}
