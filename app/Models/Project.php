<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'project_id';

    protected $fillable = [
        'project_name',
        'description',
        'ownership_type',
        'link_testing',
        'link_prod',
        'link_repository',
        'link_docs',
        'status_id',
        'health_status',
        'client_id',
        'created_by',
        'start_date',
        'end_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<ProjectStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id', 'status_id');
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id')
            ->withPivot('role_in_project', 'joined_at')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Technology, $this>
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class, 'project_technologies', 'project_id', 'tech_id')
            ->withTimestamps();
    }

    /**
     * @return HasMany<ProjectDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class, 'project_id', 'project_id');
    }

    /**
     * @return HasMany<ProjectActivityLog, $this>
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ProjectActivityLog::class, 'project_id', 'project_id');
    }

    /**
     * Scope projects visible to a given user (creator OR member), including owners.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->where('created_by', $user->id)
            ->orWhereHas('members', fn (Builder $m) => $m->where('user_id', $user->id));
    }

    public function logActivity(string $action, ?string $description = null): ProjectActivityLog
    {
        return $this->activityLogs()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
        ]);
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id', 'project_id')->orderBy('order');
    }

    /**
     * @return HasMany<ProjectMilestone, $this>
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class, 'project_id', 'project_id')->orderBy('order');
    }

    public function totalTasksCount(): int
    {
        return $this->tasks()->count();
    }

    public function completedTasksCount(): int
    {
        return $this->tasks()->where('status', 'Done')->count();
    }

    public function taskProgressPercentage(): int
    {
        $total = $this->totalTasksCount();
        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->completedTasksCount() / $total) * 100);
    }

    public function daysRemaining(): ?int
    {
        if (! $this->end_date) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->end_date->startOfDay(), false);
    }

    public function isRecentlyUpdated(): bool
    {
        return $this->updated_at->greaterThanOrEqualTo(now()->subDays(7));
    }
}
