<?php

namespace Tests\Feature\JobTracker;

use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobTrackerExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_export_downloads_owned_records_with_correct_filename(): void
    {
        $user = $this->authorizedUser();
        $other = $this->authorizedUser();

        JobApplication::factory()->create(['user_id' => $user->id, 'company_name' => 'Owner Export Co']);
        JobApplication::factory()->create(['user_id' => $other->id, 'company_name' => 'Other Export Co']);

        $this->actingAs($user);

        $response = $this->get(route('job-tracker.export.csv'));

        $response->assertOk();

        $disp = (string) $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment', $disp);
        $this->assertStringContainsString('job-tracker-', $disp);
        $this->assertStringContainsString('.csv', $disp);

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Owner Export Co', $content);
        $this->assertStringNotContainsString('Other Export Co', $content);
    }

    public function test_pdf_export_downloads_with_correct_filename(): void
    {
        $user = $this->authorizedUser();

        JobApplication::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->get(route('job-tracker.export.pdf'));

        $response->assertOk();
        $this->assertStringStartsWith('application/pdf', (string) $response->headers->get('Content-Type'));

        $disp = (string) $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('job-tracker-', $disp);
        $this->assertStringContainsString('.pdf', $disp);
    }
}
