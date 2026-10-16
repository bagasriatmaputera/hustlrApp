<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Requests\UpdateJobApplicationRequest;
use App\Models\JobApplication;
use App\Models\User;
use App\Services\JobApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JobApplicationController extends Controller
{
    public function __construct(
        protected JobApplicationService $jobApplicationService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): RedirectResponse|JsonResponse
    {
        if (! $request->user()?->can('IPS - JOBTRACKER - Can List Job Tracker')) {
            abort(Response::HTTP_FORBIDDEN, __('Unauthorized action.'));
        }

        if ($request->wantsJson()) {
            $filters = $request->only(['search', 'contract_status', 'apply_status', 'workplace_type']);
            $jobs = $this->jobApplicationService->getPaginated($filters, (int) $request->get('per_page', 10));

            return response()->json($jobs);
        }

        return redirect()->route('job-tracker.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobApplicationRequest $request): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $job = $this->jobApplicationService->createJobApplication(
            $request->validated(),
            $user->id
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('Job application created successfully.'),
                'data' => $job,
            ], Response::HTTP_CREATED);
        }

        return redirect()->route('job-tracker.index')->with('success', __('Job application created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, JobApplication $jobApplication): RedirectResponse|JsonResponse
    {
        if (! $request->user()?->can('IPS - JOBTRACKER - Can List Job Tracker')) {
            abort(Response::HTTP_FORBIDDEN, __('Unauthorized action.'));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $jobApplication->load('user'),
            ]);
        }

        return redirect()->route('job-tracker.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobApplicationRequest $request, JobApplication $jobApplication): JsonResponse|RedirectResponse
    {
        $updatedJob = $this->jobApplicationService->updateJobApplication(
            $jobApplication,
            $request->validated()
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('Job application updated successfully.'),
                'data' => $updatedJob,
            ]);
        }

        return redirect()->route('job-tracker.index')->with('success', __('Job application updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, JobApplication $jobApplication): JsonResponse|RedirectResponse
    {
        if (! $request->user()?->can('IPS - JOBTRACKER - Can Delete Job Tracker')) {
            abort(Response::HTTP_FORBIDDEN, __('Unauthorized action.'));
        }

        $this->jobApplicationService->deleteJobApplication($jobApplication);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('Job application deleted successfully.'),
            ]);
        }

        return redirect()->route('job-tracker.index')->with('success', __('Job application deleted successfully.'));
    }
}
