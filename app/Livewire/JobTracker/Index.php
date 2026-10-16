<?php

namespace App\Livewire\JobTracker;

use App\Concerns\AuthorizesPermissions;
use App\Models\JobApplication;
use App\Models\User;
use App\Services\JobApplicationService;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read LengthAwarePaginator<int, JobApplication> $jobApplications
 */
#[Title('Job Tracker')]
class Index extends Component
{
    use AuthorizesPermissions, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $contractFilter = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $workplaceFilter = '';

    // Infinite scroll batch size
    public int $perPage = 10;

    // Modals visibility state
    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDetailModal = false;

    public bool $showDeleteModal = false;

    // Form inputs for Create & Edit
    public ?string $selectedJobId = null;

    public string $company_name = '';

    public string $position = '';

    public string $contract_status = 'full_time';

    public string $apply_status = 'applied';

    public string $salary = '';

    public string $location = '';

    public string $workplace_type = 'remote';

    public string $job_url = '';

    public string $benefits = '';

    public string $notes = '';

    public string $applied_at = '';

    // Viewing details
    public ?JobApplication $viewingJob = null;

    // Delete confirmation
    public ?string $deletingJobTitle = null;

    public function mount(): void
    {
        $this->authorizePermission('IPS - JOBTRACKER - Can List Job Tracker');

        // Default applied_at to today for new records
        $this->applied_at = now()->toDateString();
    }

    /**
     * @return LengthAwarePaginator<int, JobApplication>
     */
    #[Computed]
    public function jobApplications(): LengthAwarePaginator
    {
        /** @var JobApplicationService $service */
        $service = app(JobApplicationService::class);

        return $service->getPaginated([
            'search' => $this->search,
            'contract_status' => $this->contractFilter,
            'apply_status' => $this->statusFilter,
            'workplace_type' => $this->workplaceFilter,
        ], $this->perPage);
    }

    /**
     * Infinite scroll trigger to load more items
     */
    public function loadMore(): void
    {
        if ($this->jobApplications->hasMorePages()) {
            $this->perPage += 10;
            unset($this->jobApplications);
        }
    }

    public function updatedSearch(): void
    {
        $this->perPage = 10;
        $this->resetPage();
    }

    public function updatedContractFilter(): void
    {
        $this->perPage = 10;
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->perPage = 10;
        $this->resetPage();
    }

    public function updatedWorkplaceFilter(): void
    {
        $this->perPage = 10;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'contractFilter', 'statusFilter', 'workplaceFilter']);
        $this->perPage = 10;
        $this->resetPage();
    }

    // --- METRICS COMPUTED PROPERTIES ---

    #[Computed]
    public function totalApplicationsCount(): int
    {
        return JobApplication::count();
    }

    #[Computed]
    public function hrReviewCount(): int
    {
        return JobApplication::where('apply_status', 'hr_review')->count();
    }

    #[Computed]
    public function interviewCount(): int
    {
        return JobApplication::whereIn('apply_status', ['interview', 'interview_user'])->count();
    }

    #[Computed]
    public function offeringCount(): int
    {
        return JobApplication::whereIn('apply_status', ['offering_letter', 'accepted'])->count();
    }

    #[Computed]
    public function rejectedCount(): int
    {
        return JobApplication::where('apply_status', 'rejected')->count();
    }

    // --- CREATE MODAL ACTIONS ---

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->applied_at = now()->toDateString();
        $this->showCreateModal = true;
    }

    public function createJob(): void
    {
        $this->authorizePermission('IPS - JOBTRACKER - Can Create Job Tracker');

        $validated = $this->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'contract_status' => ['required', 'string', 'in:contract,full_time,part_time,freelance,internship'],
            'apply_status' => ['required', 'string', 'in:applied,hr_review,interview,interview_user,offering_letter,accepted,rejected'],
            'salary' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'workplace_type' => ['required', 'string', 'in:remote,hybrid,onsite'],
            'job_url' => ['nullable', 'url', 'max:500'],
            'benefits' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'applied_at' => ['nullable', 'date'],
        ]);

        /** @var User $currentUser */
        $currentUser = Auth::user();

        /** @var JobApplicationService $service */
        $service = app(JobApplicationService::class);
        $service->createJobApplication($validated, $currentUser->id);

        $this->showCreateModal = false;
        $this->resetForm();

        Flux::toast(variant: 'success', text: __('Job application added successfully.'));
    }

    // --- SHOW / DETAIL MODAL ACTIONS ---

    public function openShowModal(string $jobId): void
    {
        $this->authorizePermission('IPS - JOBTRACKER - Can List Job Tracker');

        $this->viewingJob = JobApplication::with('user')->find($jobId);
        if ($this->viewingJob) {
            $this->showDetailModal = true;
        }
    }

    // --- EDIT MODAL ACTIONS ---

    public function openEditModal(string $jobId): void
    {
        $job = JobApplication::findOrFail($jobId);

        $this->selectedJobId = $job->job_application_id;
        $this->company_name = $job->company_name;
        $this->position = $job->position;
        $this->contract_status = $job->contract_status;
        $this->apply_status = $job->apply_status;
        $this->salary = $job->salary ?? '';
        $this->location = $job->location ?? '';
        $this->workplace_type = $job->workplace_type;
        $this->job_url = $job->job_url ?? '';
        $this->benefits = $job->benefits ?? '';
        $this->notes = $job->notes ?? '';
        $this->applied_at = $job->applied_at ? $job->applied_at->format('Y-m-d') : '';

        $this->showEditModal = true;
    }

    public function updateJob(): void
    {
        $this->authorizePermission('IPS - JOBTRACKER - Can Update Job Tracker');

        if (! $this->selectedJobId) {
            return;
        }

        $job = JobApplication::findOrFail($this->selectedJobId);

        $validated = $this->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'contract_status' => ['required', 'string', 'in:contract,full_time,part_time,freelance,internship'],
            'apply_status' => ['required', 'string', 'in:applied,hr_review,interview,interview_user,offering_letter,accepted,rejected'],
            'salary' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'workplace_type' => ['required', 'string', 'in:remote,hybrid,onsite'],
            'job_url' => ['nullable', 'url', 'max:500'],
            'benefits' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'applied_at' => ['nullable', 'date'],
        ]);

        /** @var JobApplicationService $service */
        $service = app(JobApplicationService::class);
        $service->updateJobApplication($job, $validated);

        $this->showEditModal = false;
        $this->resetForm();

        // Refresh viewingJob if detail modal was open
        if ($this->viewingJob && $this->viewingJob->job_application_id === $job->job_application_id) {
            $this->viewingJob = $job->fresh();
        }

        Flux::toast(variant: 'success', text: __('Job application updated successfully.'));
    }

    // --- QUICK STATUS UPDATE ---

    public function updateStatus(string $jobId, string $newStatus): void
    {
        $this->authorizePermission('IPS - JOBTRACKER - Can Update Job Tracker');

        $job = JobApplication::findOrFail($jobId);

        /** @var JobApplicationService $service */
        $service = app(JobApplicationService::class);
        $service->updateJobApplication($job, ['apply_status' => $newStatus]);

        Flux::toast(variant: 'success', text: __('Application status changed to :status', ['status' => JobApplication::getApplyStatusConfig($newStatus)['label']]));
    }

    // --- DELETE MODAL ACTIONS ---

    public function confirmDelete(string $jobId): void
    {
        $this->authorizePermission('IPS - JOBTRACKER - Can Delete Job Tracker');

        $job = JobApplication::findOrFail($jobId);

        $this->selectedJobId = $job->job_application_id;
        $this->deletingJobTitle = "{$job->position} at {$job->company_name}";
        $this->showDeleteModal = true;
    }

    public function deleteJob(): void
    {
        $this->authorizePermission('IPS - JOBTRACKER - Can Delete Job Tracker');

        if (! $this->selectedJobId) {
            $this->showDeleteModal = false;

            return;
        }

        $job = JobApplication::findOrFail($this->selectedJobId);

        /** @var JobApplicationService $service */
        $service = app(JobApplicationService::class);
        $service->deleteJobApplication($job);

        $this->showDeleteModal = false;
        if ($this->showDetailModal && $this->viewingJob?->job_application_id === $this->selectedJobId) {
            $this->showDetailModal = false;
            $this->viewingJob = null;
        }
        $this->resetForm();

        Flux::toast(variant: 'success', text: __('Job application deleted successfully.'));
    }

    private function resetForm(): void
    {
        $this->selectedJobId = null;
        $this->company_name = '';
        $this->position = '';
        $this->contract_status = 'full_time';
        $this->apply_status = 'applied';
        $this->salary = '';
        $this->location = '';
        $this->workplace_type = 'remote';
        $this->job_url = '';
        $this->benefits = '';
        $this->notes = '';
        $this->applied_at = '';
        $this->deletingJobTitle = null;
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.job-tracker.index');
    }
}
