<?php

namespace App\Livewire\Projects;

use App\Concerns\AuthorizesPermissions;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('All Projects')]
class Index extends Component
{
    use AuthorizesPermissions, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    public function mount(): void
    {
        $this->authorizePermission('IPS - PROJECT - Can List Projects');
    }

    /**
     * @return LengthAwarePaginator<int, Project>
     */
    #[Computed]
    public function projects(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = Auth::user();

        return Project::query()
            ->with(['status', 'client', 'technologies', 'members'])
            ->visibleTo($user)
            ->when($this->search, function ($query) {
                $query->where('project_name', 'like', "%{$this->search}%");
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status_id', $this->statusFilter);
            })
            ->latest('updated_at')
            ->paginate(9);
    }

    /**
     * @return Collection<int, ProjectStatus>
     */
    #[Computed]
    public function statuses(): Collection
    {
        return ProjectStatus::orderBy('status_name')->get();
    }

    #[Computed]
    public function totalProjects(): int
    {
        return Project::visibleTo(Auth::user())->count();
    }

    #[Computed]
    public function activeProjects(): int
    {
        return Project::visibleTo(Auth::user())->whereHas('status', fn ($q) => $q->where('status_name', 'In Progress'))->count();
    }

    #[Computed]
    public function completedProjects(): int
    {
        return Project::visibleTo(Auth::user())->whereHas('status', fn ($q) => $q->where('status_name', 'Completed'))->count();
    }

    #[Computed]
    public function onHoldProjects(): int
    {
        return Project::visibleTo(Auth::user())->whereHas('status', fn ($q) => $q->where('status_name', 'On Hold'))->count();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'statusFilter']);
        $this->resetPage();
    }

    public function deleteProject(string $projectId): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Delete Projects');

        $project = Project::findOrFail($projectId);

        $project->logActivity('project.deleted', __('Deleted project ":name".', ['name' => $project->project_name]));
        $project->delete();

        Flux::toast(variant: 'success', text: __('Project deleted successfully.'));
    }

    /**
     * @return array<string, string>
     */
    public static function statusColor(string $statusName): array
    {
        return match ($statusName) {
            'Planning' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-300'],
            'In Progress' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-300'],
            'On Hold' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-700 dark:text-orange-300'],
            'Completed' => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-300'],
            'Cancelled' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-300'],
            default => ['bg' => 'bg-zinc-100 dark:bg-zinc-800', 'text' => 'text-zinc-700 dark:text-zinc-300'],
        };
    }

    public function render()
    {
        return view('livewire.projects.index');
    }
}
