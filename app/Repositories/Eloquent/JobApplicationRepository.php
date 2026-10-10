<?php

namespace App\Repositories\Eloquent;

use App\Models\JobApplication;
use App\Repositories\Contracts\JobApplicationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JobApplicationRepository implements JobApplicationRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, JobApplication>
     */
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return JobApplication::query()
            ->with(['user'])
            ->when($filters['user_id'] ?? null, function ($q, $userId) {
                $q->where('user_id', $userId);
            })
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('company_name', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('salary', 'like', "%{$search}%");
                });
            })
            ->when($filters['contract_status'] ?? null, function ($q, $contractStatus) {
                $q->where('contract_status', $contractStatus);
            })
            ->when($filters['apply_status'] ?? null, function ($q, $applyStatus) {
                $q->where('apply_status', $applyStatus);
            })
            ->when($filters['workplace_type'] ?? null, function ($q, $workplaceType) {
                $q->where('workplace_type', $workplaceType);
            })
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function findById(string $id): ?JobApplication
    {
        return JobApplication::with(['user'])->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): JobApplication
    {
        return JobApplication::create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(JobApplication $jobApplication, array $data): bool
    {
        return $jobApplication->update($data);
    }

    public function delete(JobApplication $jobApplication): bool
    {
        return (bool) $jobApplication->delete();
    }
}
