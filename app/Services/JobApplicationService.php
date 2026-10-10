<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobApplicationService
{
    public function __construct(
        protected JobApplicationRepositoryInterface $jobApplicationRepository
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, JobApplication>
     */
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->jobApplicationRepository->getPaginated($filters, $perPage);
    }

    public function findById(string $id): ?JobApplication
    {
        return $this->jobApplicationRepository->findById($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createJobApplication(array $data, int $userId): JobApplication
    {
        return DB::transaction(function () use ($data, $userId): JobApplication {
            $data['user_id'] = $userId;
            $jobApplication = $this->jobApplicationRepository->create($data);

            Log::info('Job application created', [
                'job_application_id' => $jobApplication->job_application_id,
                'company_name' => $jobApplication->company_name,
                'position' => $jobApplication->position,
                'user_id' => $userId,
            ]);

            return $jobApplication;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateJobApplication(JobApplication $jobApplication, array $data): JobApplication
    {
        return DB::transaction(function () use ($jobApplication, $data): JobApplication {
            $this->jobApplicationRepository->update($jobApplication, $data);

            Log::info('Job application updated', [
                'job_application_id' => $jobApplication->job_application_id,
                'company_name' => $jobApplication->company_name,
            ]);

            return $jobApplication->fresh();
        });
    }

    public function deleteJobApplication(JobApplication $jobApplication): bool
    {
        return DB::transaction(function () use ($jobApplication): bool {
            $deleted = $this->jobApplicationRepository->delete($jobApplication);

            Log::info('Job application deleted', [
                'job_application_id' => $jobApplication->job_application_id,
            ]);

            return $deleted;
        });
    }
}
