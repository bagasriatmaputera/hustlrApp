<?php

namespace App\Livewire;

use App\Models\JobApplication;
use App\Models\Portfolio\PortfolioExperience;
use App\Models\Portfolio\PortfolioProfile;
use App\Models\Portfolio\PortfolioProject;
use App\Models\Project;
use App\Models\ProjectActivityLog;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * @property-read User $currentUser
 * @property-read string $roleLabel
 * @property-read array<string, int> $projectStats
 * @property-read array<string, int> $taskStats
 * @property-read array<string, int> $userStats
 * @property-read int|null $pendingApprovals
 * @property-read array<string, int> $jobStats
 * @property-read array<string, int> $portfolioStats
 * @property-read Collection<int, Task> $upcomingTasks
 * @property-read Collection<int, ProjectActivityLog> $recentActivity
 */
#[Title('Dashboard')]
class Dashboard extends Component
{
    #[Computed]
    public function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    #[Computed]
    public function roleLabel(): string
    {
        $role = $this->currentUser->getRoleNames()->first();

        return $role === null
            ? __('Member')
            : str_replace(' Integrated Personal System', '', $role);
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function projectStats(): array
    {
        $user = $this->currentUser;

        if (! $user->can('IPS - PROJECT - Can List Projects')) {
            return [];
        }

        $projects = Project::query()->visibleTo($user);

        return [
            'total' => (clone $projects)->count(),
            'active' => (clone $projects)->whereHas('status', fn ($query) => $query->where('status_name', 'In Progress'))->count(),
            'completed' => (clone $projects)->whereHas('status', fn ($query) => $query->where('status_name', 'Completed'))->count(),
            'overdue' => (clone $projects)
                ->whereNotNull('end_date')
                ->whereDate('end_date', '<', today())
                ->whereHas('status', fn ($query) => $query->whereNotIn('status_name', ['Completed', 'Cancelled']))
                ->count(),
        ];
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function taskStats(): array
    {
        $user = $this->currentUser;

        if (! $user->can('IPS - PROJECT - Can List Tasks')) {
            return [];
        }

        $tasks = Task::query()->whereIn(
            'project_id',
            Project::query()->visibleTo($user)->select('project_id'),
        );

        return [
            'mine_open' => (clone $tasks)->where('assigned_to', $user->id)->where('status', '!=', 'Done')->count(),
            'open' => (clone $tasks)->where('status', '!=', 'Done')->count(),
            'overdue' => (clone $tasks)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', today())
                ->where('status', '!=', 'Done')
                ->count(),
            'due_soon' => (clone $tasks)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '>=', today())
                ->whereDate('due_date', '<=', today()->addDays(7))
                ->where('status', '!=', 'Done')
                ->count(),
        ];
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function userStats(): array
    {
        $user = $this->currentUser;

        if (! $user->can('IPS - UAM - Can List Users')) {
            return [];
        }

        return [
            'total' => User::count(),
            'pending' => User::where('approval_status', 'pending')->count(),
            'approved' => User::where('approval_status', 'approved')->count(),
        ];
    }

    #[Computed]
    public function pendingApprovals(): ?int
    {
        if (! $this->currentUser->hasRole('Owner Integrated Personal System')) {
            return null;
        }

        return User::where('approval_status', 'pending')->count();
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function jobStats(): array
    {
        $user = $this->currentUser;

        if (! $user->can('IPS - JOBTRACKER - Can List Job Tracker')) {
            return [];
        }

        $applications = JobApplication::query()->where('user_id', $user->id);

        return [
            'total' => (clone $applications)->count(),
            'interview' => (clone $applications)->whereIn('apply_status', ['interview', 'interview_user'])->count(),
            'offering' => (clone $applications)->whereIn('apply_status', ['offering_letter', 'accepted'])->count(),
            'rejected' => (clone $applications)->where('apply_status', 'rejected')->count(),
        ];
    }

    /**
     * @return array<string, int>
     */
    #[Computed]
    public function portfolioStats(): array
    {
        if (! $this->currentUser->can('PORTFOLIO - Can Manage Portfolio')) {
            return [];
        }

        return [
            'profile_completed' => PortfolioProfile::query()->exists() ? 1 : 0,
            'projects' => PortfolioProject::query()->count(),
            'experiences' => PortfolioExperience::query()->count(),
        ];
    }

    /**
     * @return Collection<int, Task>
     */
    #[Computed]
    public function upcomingTasks(): Collection
    {
        $user = $this->currentUser;

        if (! $user->can('IPS - PROJECT - Can List Tasks')) {
            return new Collection;
        }

        return Task::query()
            ->whereIn('project_id', Project::query()->visibleTo($user)->select('project_id'))
            ->where('status', '!=', 'Done')
            ->whereNotNull('due_date')
            ->with('project')
            ->orderBy('due_date')
            ->limit(6)
            ->get();
    }

    /**
     * @return Collection<int, ProjectActivityLog>
     */
    #[Computed]
    public function recentActivity(): Collection
    {
        $user = $this->currentUser;

        if (! $user->can('IPS - PROJECT - Can List Projects')) {
            return new Collection;
        }

        return ProjectActivityLog::query()
            ->whereIn('project_id', Project::query()->visibleTo($user)->select('project_id'))
            ->with(['project', 'user'])
            ->latest('created_at')
            ->limit(6)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.dashboard');
    }
}
