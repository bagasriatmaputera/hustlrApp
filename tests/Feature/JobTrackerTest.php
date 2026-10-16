<?php

namespace Tests\Feature;

use App\Livewire\JobTracker\Index;
use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class JobTrackerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_job_tracker_page(): void
    {
        $user = $this->authorizedUser();
        JobApplication::factory()->create([
            'user_id' => $user->id,
            'company_name' => 'Tech Nusantara',
            'position' => 'Senior Laravel Developer',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('job-tracker.index'));
        $response->assertStatus(200);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Tech Nusantara')
            ->assertSee('Senior Laravel Developer');
    }

    public function test_user_can_create_job_application_via_livewire_modal(): void
    {
        $user = $this->authorizedUser();
        $this->actingAs($user);

        Livewire::test(Index::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->set('company_name', 'Bukalapak Tech')
            ->set('position', 'Staff Software Engineer')
            ->set('contract_status', 'full_time')
            ->set('apply_status', 'hr_review')
            ->set('salary', 'Rp 30.000.000')
            ->set('location', 'Jakarta Selatan')
            ->set('workplace_type', 'hybrid')
            ->set('job_url', 'https://bukalapak.com/careers/123')
            ->set('benefits', "MacBook Pro\nBPJS")
            ->set('notes', 'HR scheduled call for tomorrow')
            ->call('createJob')
            ->assertSet('showCreateModal', false);

        $this->assertDatabaseHas('job_applications', [
            'company_name' => 'Bukalapak Tech',
            'position' => 'Staff Software Engineer',
            'contract_status' => 'full_time',
            'apply_status' => 'hr_review',
        ]);
    }

    public function test_user_can_view_and_update_job_application(): void
    {
        $user = $this->authorizedUser();
        $job = JobApplication::factory()->create([
            'user_id' => $user->id,
            'company_name' => 'Initial Company',
            'position' => 'Junior Developer',
            'apply_status' => 'applied',
        ]);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->call('openShowModal', $job->job_application_id)
            ->assertSet('showDetailModal', true)
            ->call('openEditModal', $job->job_application_id)
            ->assertSet('showEditModal', true)
            ->assertSet('company_name', 'Initial Company')
            ->set('company_name', 'Updated Company Name')
            ->set('position', 'Lead Developer')
            ->set('apply_status', 'interview')
            ->call('updateJob')
            ->assertSet('showEditModal', false);

        $this->assertDatabaseHas('job_applications', [
            'job_application_id' => $job->job_application_id,
            'company_name' => 'Updated Company Name',
            'position' => 'Lead Developer',
            'apply_status' => 'interview',
        ]);
    }

    public function test_user_can_update_apply_status_directly(): void
    {
        $user = $this->authorizedUser();
        $job = JobApplication::factory()->create([
            'user_id' => $user->id,
            'apply_status' => 'applied',
        ]);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->call('updateStatus', $job->job_application_id, 'offering_letter');

        $this->assertDatabaseHas('job_applications', [
            'job_application_id' => $job->job_application_id,
            'apply_status' => 'offering_letter',
        ]);
    }

    public function test_user_can_delete_job_application(): void
    {
        $user = $this->authorizedUser();
        $job = JobApplication::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->call('confirmDelete', $job->job_application_id)
            ->assertSet('showDeleteModal', true)
            ->call('deleteJob')
            ->assertSet('showDeleteModal', false);

        $this->assertSoftDeleted('job_applications', [
            'job_application_id' => $job->job_application_id,
        ]);
    }

    public function test_infinite_scroll_loads_more_records(): void
    {
        $user = $this->authorizedUser();
        JobApplication::factory()->count(25)->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(Index::class)
            ->assertSet('perPage', 10);

        $this->assertCount(10, $component->get('jobApplications'));

        $component->call('loadMore')
            ->assertSet('perPage', 20);

        $this->assertCount(20, $component->get('jobApplications'));
    }

    public function test_controller_api_endpoints(): void
    {
        $user = $this->authorizedUser();
        $this->actingAs($user);

        // Test POST store API
        $response = $this->postJson(route('jobs.store'), [
            'company_name' => 'Shopee Indonesia',
            'position' => 'Senior Backend Engineer',
            'contract_status' => 'full_time',
            'apply_status' => 'applied',
            'salary' => 'Rp 25.000.000',
            'location' => 'Jakarta Selatan',
            'workplace_type' => 'hybrid',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.company_name', 'Shopee Indonesia');

        $jobId = $response->json('data.job_application_id');

        // Test GET index API
        $indexResponse = $this->getJson(route('jobs.index'));
        $indexResponse->assertStatus(200);

        // Test GET show API
        $showResponse = $this->getJson(route('jobs.show', $jobId));
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.position', 'Senior Backend Engineer');

        // Test PUT update API
        $updateResponse = $this->putJson(route('jobs.update', $jobId), [
            'company_name' => 'Shopee Indonesia',
            'position' => 'Principal Backend Engineer',
            'contract_status' => 'full_time',
            'apply_status' => 'interview_user',
            'workplace_type' => 'hybrid',
        ]);
        $updateResponse->assertStatus(200);

        // Test DELETE API
        $deleteResponse = $this->deleteJson(route('jobs.destroy', $jobId));
        $deleteResponse->assertStatus(200);
    }
}
