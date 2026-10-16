<?php

namespace Tests\Feature\Approvals;

use App\Livewire\Approvals\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AccessApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_owner_cannot_view_approvals_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Index::class)->assertStatus(403);
    }

    public function test_owner_can_view_approvals_page_with_pending_users(): void
    {
        $owner = $this->authorizedUser();
        User::factory()->pending()->create(['full_name' => 'Pending Person', 'email' => 'pending@test.com']);

        $this->actingAs($owner);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Pending Person')
            ->assertSee('pending@test.com');
    }

    public function test_owner_can_approve_user_and_grant_requested_module_permissions(): void
    {
        $owner = $this->authorizedUser();
        $pending = User::factory()->pending()->create([
            'requested_modules' => ['project_management'],
        ]);

        $this->actingAs($owner);

        Livewire::test(Index::class)->call('approve', $pending->id);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $pending->refresh();

        $this->assertSame('approved', $pending->approval_status);
        $this->assertTrue($pending->can('IPS - PROJECT - Can List Projects'));
        $this->assertFalse($pending->can('IPS - JOBTRACKER - Can List Job Tracker'));
    }

    public function test_owner_can_reject_user(): void
    {
        $owner = $this->authorizedUser();
        $pending = User::factory()->pending()->create([
            'requested_modules' => ['job_tracker'],
        ]);

        $this->actingAs($owner);

        Livewire::test(Index::class)->call('reject', $pending->id);

        $pending->refresh();

        $this->assertSame('rejected', $pending->approval_status);
        $this->assertTrue($pending->isRejected());
    }
}
