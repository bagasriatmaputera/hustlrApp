<?php

namespace Tests\Feature\Projects;

use App\Livewire\Projects\Show;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $status = ProjectStatus::create(['status_name' => 'In Progress']);
        $user = $this->authorizedUser();

        $project = Project::create([
            'project_name' => 'Secret Project',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'created_by' => $user->id,
        ]);

        $response = $this->get(route('projects.show', $project->project_id));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_project_overview_and_header(): void
    {
        $user = $this->authorizedUser();
        $status = ProjectStatus::create(['status_name' => 'In Progress']);

        $project = Project::create([
            'project_name' => 'Rocket App',
            'description' => 'Build a next gen platform',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'health_status' => 'On Track',
            'created_by' => $user->id,
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->addDays(20)->toDateString(),
            'link_repository' => 'https://github.com/example/repo',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('projects.show', $project->project_id));
        $response->assertOk();
        $response->assertSee('Rocket App');
        $response->assertSee('Build a next gen platform');
        $response->assertSee('On Track');
        $response->assertSee('Board View');
        $response->assertSee('Roadmap / Milestones');
    }

    public function test_user_can_create_task_and_view_on_board(): void
    {
        $user = $this->authorizedUser();
        $status = ProjectStatus::create(['status_name' => 'In Progress']);

        $project = Project::create([
            'project_name' => 'Task Tracker Pro',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->set('taskTitle', 'Design Database Schema')
            ->set('taskDescription', 'Draft ERD and relations')
            ->set('taskStatus', 'In Progress')
            ->set('taskPriority', 'High')
            ->set('taskCategory', 'Database')
            ->set('newSubtasks', ['Create migrations', 'Write factories'])
            ->call('createTask')
            ->assertHasNoErrors()
            ->assertSee('Design Database Schema');

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->project_id,
            'title' => 'Design Database Schema',
            'status' => 'In Progress',
            'priority' => 'High',
            'category' => 'Database',
        ]);

        $this->assertDatabaseHas('task_checklists', [
            'title' => 'Create migrations',
            'is_completed' => false,
        ]);
    }

    public function test_user_can_create_task_with_pic_assigned_user(): void
    {
        $user = $this->authorizedUser(['name' => 'Alice Developer', 'full_name' => 'Alice Developer']);
        $assignee = User::factory()->create(['name' => 'Bob Engineer', 'full_name' => 'Bob Engineer']);
        $status = ProjectStatus::create(['status_name' => 'In Progress']);

        $project = Project::create([
            'project_name' => 'Task Tracker User PIC',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->set('taskTitle', 'Integrate Payment Gateway')
            ->set('taskPicType', 'user')
            ->set('taskAssignedTo', $assignee->id)
            ->call('createTask')
            ->assertHasNoErrors()
            ->assertSee('Bob Engineer');

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->project_id,
            'title' => 'Integrate Payment Gateway',
            'assigned_to' => $assignee->id,
            'pic_name' => null,
        ]);
    }

    public function test_user_can_create_task_with_pic_input_manually(): void
    {
        $user = $this->authorizedUser(['name' => 'Alice Developer']);
        $status = ProjectStatus::create(['status_name' => 'In Progress']);

        $project = Project::create([
            'project_name' => 'Task Tracker Manual PIC',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->set('taskTitle', 'External PenTest Review')
            ->set('taskPicType', 'manual')
            ->set('taskPicName', 'External Auditor Team')
            ->call('createTask')
            ->assertHasNoErrors()
            ->assertSee('External Auditor Team');

        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->project_id,
            'title' => 'External PenTest Review',
            'assigned_to' => null,
            'pic_name' => 'External Auditor Team',
        ]);
    }

    public function test_user_can_update_task_status(): void
    {
        $user = $this->authorizedUser();
        $status = ProjectStatus::create(['status_name' => 'In Progress']);

        $project = Project::create([
            'project_name' => 'Workflow App',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'created_by' => $user->id,
        ]);

        $task = Task::create([
            'project_id' => $project->project_id,
            'title' => 'Fix Login Bug',
            'status' => 'Backlog',
            'priority' => 'High',
            'category' => 'Bug',
        ]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->call('updateTaskStatus', $task->task_id, 'Done')
            ->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'task_id' => $task->task_id,
            'status' => 'Done',
        ]);
    }

    public function test_user_can_toggle_subtask_checklist(): void
    {
        $user = $this->authorizedUser();
        $status = ProjectStatus::create(['status_name' => 'In Progress']);

        $project = Project::create([
            'project_name' => 'Subtask App',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'created_by' => $user->id,
        ]);

        $task = Task::create([
            'project_id' => $project->project_id,
            'title' => 'Setup Auth',
            'status' => 'In Progress',
            'priority' => 'Medium',
        ]);

        $chk = TaskChecklist::create([
            'task_id' => $task->task_id,
            'title' => 'Add Passkeys',
            'is_completed' => false,
        ]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->call('toggleChecklist', $chk->checklist_id)
            ->assertStatus(200);

        $this->assertDatabaseHas('task_checklists', [
            'checklist_id' => $chk->checklist_id,
            'is_completed' => true,
        ]);
    }

    public function test_user_can_create_and_manage_milestones(): void
    {
        $user = $this->authorizedUser();
        $status = ProjectStatus::create(['status_name' => 'In Progress']);

        $project = Project::create([
            'project_name' => 'Roadmap App',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->set('activeTab', 'roadmap')
            ->set('milestoneTitle', 'Sprint 1: Setup Database & Autentikasi')
            ->set('milestoneDescription', 'Core foundation sprint')
            ->set('milestoneStatus', 'In Progress')
            ->call('createMilestone')
            ->assertHasNoErrors()
            ->assertSee('Sprint 1: Setup Database & Autentikasi');

        $this->assertDatabaseHas('project_milestones', [
            'project_id' => $project->project_id,
            'title' => 'Sprint 1: Setup Database & Autentikasi',
            'status' => 'In Progress',
        ]);
    }

    public function test_user_can_update_project_health_status(): void
    {
        $user = $this->authorizedUser();
        $status = ProjectStatus::create(['status_name' => 'In Progress']);

        $project = Project::create([
            'project_name' => 'Health Status Project',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'health_status' => 'On Track',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->call('updateHealthStatus', 'At Risk')
            ->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'project_id' => $project->project_id,
            'health_status' => 'At Risk',
        ]);
    }
}
