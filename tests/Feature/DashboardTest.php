<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('No modules assigned yet');
    }

    public function test_owner_sees_all_dashboard_modules_and_pending_approvals(): void
    {
        $owner = $this->authorizedUser(['full_name' => 'Owner Person']);
        User::factory()->pending()->create();

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Access approvals')
            ->assertSee('Projects overview')
            ->assertSee('Tasks overview')
            ->assertSee('User access')
            ->assertSee('Job applications')
            ->assertSee('Portfolio');
    }

    public function test_staff_only_sees_project_and_task_modules(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $staff = User::factory()->create();
        $staff->assignRole('Staff Integrated Personal System');

        $this->actingAs($staff)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Projects overview')
            ->assertSee('Tasks overview')
            ->assertDontSee('Access approvals')
            ->assertDontSee('User access')
            ->assertDontSee('Job applications')
            ->assertDontSee('Portfolio');
    }

    public function test_management_only_sees_modules_they_can_access(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $management = User::factory()->create();
        $management->assignRole('Management Integrated Personal System');

        $this->actingAs($management)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Projects overview')
            ->assertSee('Tasks overview')
            ->assertSee('User access')
            ->assertDontSee('Access approvals')
            ->assertDontSee('Job applications')
            ->assertDontSee('Portfolio');
    }

    public function test_user_with_only_job_tracker_permission_sees_job_module(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->givePermissionTo('IPS - JOBTRACKER - Can List Job Tracker');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Job applications')
            ->assertDontSee('Projects overview')
            ->assertDontSee('Tasks overview')
            ->assertDontSee('User access')
            ->assertDontSee('Portfolio');
    }

    public function test_user_with_only_portfolio_permission_sees_portfolio_module(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->givePermissionTo('PORTFOLIO - Can Manage Portfolio');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Portfolio')
            ->assertDontSee('Access approvals')
            ->assertDontSee('Projects overview')
            ->assertDontSee('Tasks overview')
            ->assertDontSee('User access')
            ->assertDontSee('Job applications');
    }

    public function test_staff_only_sees_tasks_from_visible_projects(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $staff = User::factory()->create();
        $staff->assignRole('Staff Integrated Personal System');

        $visibleProject = Project::factory()->create(['created_by' => $staff->id]);
        $hiddenProject = Project::factory()->create();

        Task::factory()->create([
            'project_id' => $visibleProject->project_id,
            'title' => 'Visible dashboard task',
            'status' => 'In Progress',
            'due_date' => now()->addDay(),
        ]);

        Task::factory()->create([
            'project_id' => $hiddenProject->project_id,
            'title' => 'Hidden dashboard task',
            'status' => 'In Progress',
            'due_date' => now()->addDay(),
        ]);

        $this->actingAs($staff)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Visible dashboard task')
            ->assertDontSee('Hidden dashboard task');
    }
}
