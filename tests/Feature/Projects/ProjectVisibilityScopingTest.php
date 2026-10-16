<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectVisibilityScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_sees_their_own_project(): void
    {
        $owner = $this->authorizedUser();
        $project = Project::create([
            'project_name' => 'Mine',
            'ownership_type' => 'Internal',
            'status_id' => ProjectStatus::create(['status_name' => 'In Progress'])->status_id,
            'created_by' => $owner->id,
        ]);

        $this->actingAs($owner);

        $this->get(route('projects.index'))->assertOk()->assertSee('Mine');
        $this->get(route('projects.show', $project->project_id))->assertOk();
    }

    public function test_user_sees_project_they_joined_as_member(): void
    {
        $alice = $this->authorizedUser();
        $bob = $this->authorizedUser();

        $project = Project::create([
            'project_name' => 'Joint',
            'ownership_type' => 'Internal',
            'status_id' => ProjectStatus::create(['status_name' => 'In Progress'])->status_id,
            'created_by' => $bob->id,
        ]);

        $project->members()->attach($alice->id, [
            'role_in_project' => 'Member',
            'joined_at' => now(),
        ]);

        $this->actingAs($alice);

        $this->get(route('projects.index'))->assertOk()->assertSee('Joint');
        $this->get(route('projects.show', $project->project_id))->assertOk();
    }

    public function test_user_does_not_see_projects_they_are_not_part_of(): void
    {
        $alice = $this->authorizedUser();
        $bob = $this->authorizedUser();

        Project::create([
            'project_name' => 'Bobs Secret',
            'ownership_type' => 'Internal',
            'status_id' => ProjectStatus::create(['status_name' => 'In Progress'])->status_id,
            'created_by' => $bob->id,
        ]);

        $this->actingAs($alice);

        $this->get(route('projects.index'))
            ->assertOk()
            ->assertDontSee('Bobs Secret');

        $foreign = Project::where('project_name', 'Bobs Secret')->first();

        $this->get(route('projects.show', $foreign->project_id))->assertForbidden();
        $this->get(route('projects.edit', $foreign->project_id))->assertForbidden();
    }

    public function test_owner_is_also_restricted_to_their_own_projects(): void
    {
        $alice = $this->authorizedUser();
        $bob = $this->authorizedUser();

        $foreign = Project::create([
            'project_name' => 'Bobs Stuff',
            'ownership_type' => 'Internal',
            'status_id' => ProjectStatus::create(['status_name' => 'In Progress'])->status_id,
            'created_by' => $bob->id,
        ]);

        $this->actingAs($alice);

        $this->get(route('projects.index'))->assertOk()->assertDontSee('Bobs Stuff');
        $this->get(route('projects.show', $foreign->project_id))->assertForbidden();
    }
}
