<?php

namespace App\Http\Controllers;

use App\Services\JobApplicationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobApplicationExportController extends Controller
{
    public function __construct(
        protected JobApplicationService $jobApplicationService
    ) {}

    public function csv(Request $request): StreamedResponse
    {
        if (! $request->user()?->can('IPS - JOBTRACKER - Can List Job Tracker')) {
            abort(Response::HTTP_FORBIDDEN, __('Unauthorized action.'));
        }

        $filters = $this->filters($request);
        $jobs = $this->jobApplicationService->getForExport($filters);

        $filename = $this->exportFilename($request, 'csv');

        return response()->streamDownload(function () use ($jobs): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                abort(Response::HTTP_INTERNAL_SERVER_ERROR, __('Unable to generate export.'));
            }

            fputcsv($handle, [
                __('Company'), __('Position'), __('Contract'), __('Apply Status'),
                __('Salary'), __('Location'), __('Workplace'), __('Applied At'),
            ]);

            foreach ($jobs as $job) {
                fputcsv($handle, [
                    $job->company_name,
                    $job->position,
                    $job->contract_status,
                    $job->apply_status,
                    $job->salary,
                    $job->location,
                    $job->workplace_type,
                    $job->applied_at?->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function pdf(Request $request): Response
    {
        if (! $request->user()?->can('IPS - JOBTRACKER - Can List Job Tracker')) {
            abort(Response::HTTP_FORBIDDEN, __('Unauthorized action.'));
        }

        $filters = $this->filters($request);
        $jobs = $this->jobApplicationService->getForExport($filters);

        $filename = $this->exportFilename($request, 'pdf');

        return Pdf::loadView('pdf.job-tracker', [
            'jobs' => $jobs,
            'user' => $request->user(),
        ])
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    /**
     * @return array<string, mixed>
     */
    private function filters(Request $request): array
    {
        $filters = $request->only(['search', 'contract_status', 'apply_status', 'workplace_type']);
        $filters['user_id'] = $request->user()->id;

        return $filters;
    }

    private function exportFilename(Request $request, string $extension): string
    {
        $user = $request->user();
        $slug = Str::slug($user->full_name ?? $user->name);

        return 'job-tracker-'.$slug.'-'.now()->format('Ymd-His').'.'.$extension;
    }
}
