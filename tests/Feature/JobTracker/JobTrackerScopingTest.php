<?php

namespace Tests\Feature\JobTracker;

use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobTrackerScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_only_sees_their_own_applications(): void
    {
        $alice = $this->authorizedUser();
        $bob = $this->authorizedUser();

        JobApplication::factory()->create(['user_id' => $alice->id, 'company_name' => 'Alice Co']);
        JobApplication::factory()->create(['user_id' => $bob->id, 'company_name' => 'Bob Co']);

        $this->actingAs($alice);

        $this->get(route('job-tracker.index'))
            ->assertOk()
            ->assertSee('Alice Co')
            ->assertDontSee('Bob Co');
    }

    public function test_owner_only_sees_their_own_applications(): void
    {
        $owner = $this->authorizedUser();
        $other = $this->authorizedUser();

        JobApplication::factory()->create(['user_id' => $owner->id, 'company_name' => 'Owner Co']);
        JobApplication::factory()->create(['user_id' => $other->id, 'company_name' => 'Other Co']);

        $this->actingAs($owner);

        $this->get(route('job-tracker.index'))
            ->assertOk()
            ->assertSee('Owner Co')
            ->assertDontSee('Other Co');
    }

    public function test_user_cannot_access_another_users_record(): void
    {
        $alice = $this->authorizedUser();
        $bob = $this->authorizedUser();

        $bobJob = JobApplication::factory()->create(['user_id' => $bob->id]);

        $this->actingAs($alice);

        $this->getJson(route('jobs.show', $bobJob->job_application_id))->assertNotFound();
        $this->putJson(route('jobs.update', $bobJob->job_application_id), [
            'company_name' => 'Bob Co',
            'contract_status' => 'full_time',
            'position' => 'X',
            'apply_status' => 'applied',
            'workplace_type' => 'remote',
        ])->assertNotFound();
        $this->deleteJson(route('jobs.destroy', $bobJob->job_application_id))->assertNotFound();
    }
}
