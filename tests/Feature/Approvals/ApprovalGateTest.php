<?php

namespace Tests\Feature\Approvals;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_user_is_redirected_to_waiting_page(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
            ->get(route('projects.index'))
            ->assertRedirect(route('approval.pending'));
    }

    public function test_pending_user_can_view_waiting_page(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
            ->get(route('approval.pending'))
            ->assertOk()
            ->assertSee('Waiting for approval');
    }

    public function test_approved_user_without_permission_is_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('projects.index'))
            ->assertForbidden();
    }

    public function test_owner_is_treated_as_approved(): void
    {
        $owner = $this->authorizedUser();

        $this->actingAs($owner)
            ->get(route('approvals.index'))
            ->assertOk();
    }
}
