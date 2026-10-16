<?php

namespace App\Repositories\Contracts;

use App\Models\JobApplication;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface JobApplicationRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, JobApplication>
     */
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, JobApplication>
     */
    public function getForExport(array $filters = []): Collection;

    public function findById(string $id): ?JobApplication;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): JobApplication;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(JobApplication $jobApplication, array $data): bool;

    public function delete(JobApplication $jobApplication): bool;
}
