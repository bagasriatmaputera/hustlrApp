<?php

namespace App\Livewire\Projects;

use App\Concerns\AuthorizesPermissions;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Project Overview')]
class Show extends Component
{
    use AuthorizesPermissions;

    public Project $project;

    #[Url]
    public string $activeTab = 'board';

    // Task Creation Form Properties
    public bool $showTaskModal = false;

    public string $taskTitle = '';

    public string $taskDescription = '';

    public string $taskStatus = 'Backlog';

    public string $taskPriority = 'Medium';

    public string $taskCategory = 'Backend';

    public ?string $taskDueDate = null;

    public string $taskPicType = 'user'; // 'user' or 'manual'

    public ?int $taskAssignedTo = null;

    public string $taskPicName = '';

    /** @var array<int, string> */
    public array $newSubtasks = [];

    /** @var array<int, array{id: string, title: string}> */
    public array $existingSubtasks = [];

    public string $tempSubtask = '';

    public ?string $editingTaskId = null;

    // Milestone Creation Form Properties
    public bool $showMilestoneModal = false;

    public string $milestoneTitle = '';

    public string $milestoneDescription = '';

    public string $milestoneStatus = 'Upcoming';

    public ?string $milestoneDueDate = null;

    // Quick Links Modal
    public bool $showLinksModal = false;

    public ?string $linkRepository = null;

    public ?string $linkDocs = null;

    public ?string $linkTesting = null;

    public ?string $linkProd = null;

    public function mount(Project $project): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Show Projects');

        $this->project = $project->load([
            'status',
            'client',
            'technologies',
            'members',
            'tasks.checklists',
            'milestones',
        ]);

        $this->linkRepository = $project->link_repository;
        $this->linkDocs = $project->link_docs;
        $this->linkTesting = $project->link_testing;
        $this->linkProd = $project->link_prod;
    }

    /**
     * @return array<string, Collection<int, Task>>
     */
    #[Computed]
    public function boardColumns(): array
    {
        $allTasks = $this->project->tasks()->with(['checklists', 'assignedUser'])->orderBy('order')->get();

        return [
            'Backlog' => $allTasks->where('status', 'Backlog')->values(),
            'In Progress' => $allTasks->where('status', 'In Progress')->values(),
            'In Review' => $allTasks->where('status', 'In Review')->values(),
            'Done' => $allTasks->where('status', 'Done')->values(),
        ];
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function availableUsers(): Collection
    {
        return User::orderBy('name')->get();
    }

    /**
     * @return Collection<int, ProjectMilestone>
     */
    #[Computed]
    public function milestones(): Collection
    {
        return $this->project->milestones()->orderBy('due_date')->orderBy('order')->get();
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['board', 'roadmap'])) {
            $this->activeTab = $tab;
        }
    }

    public function updateHealthStatus(string $health): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Update Projects');

        if (! in_array($health, ['On Track', 'At Risk', 'Delayed', 'Completed'])) {
            return;
        }

        $this->project->update(['health_status' => $health]);
        $this->project->refresh();

        Flux::toast(variant: 'success', text: __('Project health updated to :health.', ['health' => $health]));
    }

    // --- Task Actions ---

    public function openTaskModal(string $defaultStatus = 'Backlog'): void
    {
        $this->reset([
            'taskTitle',
            'taskDescription',
            'taskDueDate',
            'newSubtasks',
            'tempSubtask',
            'taskAssignedTo',
            'taskPicName',
        ]);
        $this->taskPicType = 'user';
        $this->taskStatus = $defaultStatus;
        $this->taskPriority = 'Medium';
        $this->taskCategory = 'Backend';
        $this->editingTaskId = null;
        $this->existingSubtasks = [];
        $this->showTaskModal = true;
    }

    public function openEditTaskModal(string $taskId): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Update Tasks');

        $task = Task::where('project_id', $this->project->project_id)
            ->with('checklists')
            ->findOrFail($taskId);

        $this->editingTaskId = $task->task_id;
        $this->taskTitle = $task->title;
        $this->taskDescription = $task->description ?? '';
        $this->taskStatus = $task->status;
        $this->taskPriority = $task->priority;
        $this->taskCategory = $task->category ?? 'Backend';
        $this->taskDueDate = $task->due_date?->format('Y-m-d');
        $this->taskAssignedTo = $task->assigned_to;
        $this->taskPicType = $task->assigned_to !== null ? 'user' : 'manual';
        $this->taskPicName = $task->pic_name ?? '';

        $this->existingSubtasks = $task->checklists
            ->map(fn ($chk): array => ['id' => $chk->checklist_id, 'title' => $chk->title])
            ->values()
            ->all();

        $this->newSubtasks = [];
        $this->tempSubtask = '';
        $this->resetValidation();
        $this->showTaskModal = true;
    }

    public function removeExistingSubtask(int $index): void
    {
        if (isset($this->existingSubtasks[$index])) {
            unset($this->existingSubtasks[$index]);
            $this->existingSubtasks = array_values($this->existingSubtasks);
        }
    }

    public function updateTask(): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Update Tasks');

        if ($this->editingTaskId === null) {
            return;
        }

        $this->validate([
            'taskTitle' => ['required', 'string', 'max:255'],
            'taskDescription' => ['nullable', 'string'],
            'taskStatus' => ['required', 'in:Backlog,In Progress,In Review,Done'],
            'taskPriority' => ['required', 'in:Low,Medium,High'],
            'taskCategory' => ['required', 'string', 'max:50'],
            'taskDueDate' => ['nullable', 'date'],
            'taskPicType' => ['required', 'in:user,manual'],
            'taskAssignedTo' => ['nullable', 'exists:users,id'],
            'taskPicName' => ['nullable', 'string', 'max:100'],
            'existingSubtasks.*.id' => ['required', 'string'],
            'existingSubtasks.*.title' => ['required', 'string', 'max:255'],
        ]);

        $task = Task::where('project_id', $this->project->project_id)->findOrFail($this->editingTaskId);

        $assignedTo = $this->taskPicType === 'user' ? $this->taskAssignedTo : null;
        $picName = $this->taskPicType === 'manual' ? trim($this->taskPicName) : null;
        if ($picName === '') {
            $picName = null;
        }

        $task->update([
            'title' => $this->taskTitle,
            'description' => $this->taskDescription ?: null,
            'status' => $this->taskStatus,
            'priority' => $this->taskPriority,
            'category' => $this->taskCategory,
            'due_date' => $this->taskDueDate ?: null,
            'assigned_to' => $assignedTo,
            'pic_name' => $picName,
        ]);

        $keptIds = [];
        $nextOrder = 0;

        foreach ($this->existingSubtasks as $row) {
            $checklist = $task->checklists()->find($row['id']);
            if ($checklist !== null) {
                $checklist->update(['title' => $row['title'], 'order' => $nextOrder]);
                $keptIds[] = $row['id'];
            }
            $nextOrder++;
        }

        $task->checklists()->whereNotIn('checklist_id', $keptIds)->delete();

        foreach ($this->newSubtasks as $subtaskTitle) {
            $task->checklists()->create([
                'title' => $subtaskTitle,
                'is_completed' => false,
                'order' => $nextOrder,
            ]);
            $nextOrder++;
        }

        $this->showTaskModal = false;
        $this->editingTaskId = null;
        $this->existingSubtasks = [];
        $this->reset([
            'taskTitle',
            'taskDescription',
            'taskDueDate',
            'newSubtasks',
            'tempSubtask',
            'taskAssignedTo',
            'taskPicName',
        ]);
        $this->project->refresh();
        unset($this->boardColumns);

        Flux::toast(variant: 'success', text: __('Task updated successfully.'));
    }

    public function addSubtaskItem(): void
    {
        $trimmed = trim($this->tempSubtask);
        if ($trimmed !== '') {
            $this->newSubtasks[] = $trimmed;
            $this->tempSubtask = '';
        }
    }

    public function removeSubtaskItem(int $index): void
    {
        if (isset($this->newSubtasks[$index])) {
            unset($this->newSubtasks[$index]);
            $this->newSubtasks = array_values($this->newSubtasks);
        }
    }

    public function createTask(): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Create Tasks');

        $this->validate([
            'taskTitle' => ['required', 'string', 'max:255'],
            'taskDescription' => ['nullable', 'string'],
            'taskStatus' => ['required', 'in:Backlog,In Progress,In Review,Done'],
            'taskPriority' => ['required', 'in:Low,Medium,High'],
            'taskCategory' => ['required', 'string', 'max:50'],
            'taskDueDate' => ['nullable', 'date'],
            'taskPicType' => ['required', 'in:user,manual'],
            'taskAssignedTo' => ['nullable', 'exists:users,id'],
            'taskPicName' => ['nullable', 'string', 'max:100'],
        ]);

        $maxOrder = $this->project->tasks()->where('status', $this->taskStatus)->max('order') ?? 0;

        $assignedTo = $this->taskPicType === 'user' ? $this->taskAssignedTo : null;
        $picName = $this->taskPicType === 'manual' ? trim($this->taskPicName) : null;
        if ($picName === '') {
            $picName = null;
        }

        $task = $this->project->tasks()->create([
            'title' => $this->taskTitle,
            'description' => $this->taskDescription ?: null,
            'status' => $this->taskStatus,
            'priority' => $this->taskPriority,
            'category' => $this->taskCategory,
            'due_date' => $this->taskDueDate ?: null,
            'order' => $maxOrder + 1,
            'assigned_to' => $assignedTo,
            'pic_name' => $picName,
        ]);

        foreach ($this->newSubtasks as $idx => $subtaskTitle) {
            $task->checklists()->create([
                'title' => $subtaskTitle,
                'is_completed' => false,
                'order' => $idx,
            ]);
        }

        $this->showTaskModal = false;
        $this->reset([
            'taskTitle',
            'taskDescription',
            'taskDueDate',
            'newSubtasks',
            'tempSubtask',
            'taskAssignedTo',
            'taskPicName',
        ]);
        $this->project->refresh();
        unset($this->boardColumns);

        Flux::toast(variant: 'success', text: __('Task created successfully.'));
    }

    public function updateTaskStatus(string $taskId, string $newStatus): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Update Tasks');

        if (! in_array($newStatus, ['Backlog', 'In Progress', 'In Review', 'Done'])) {
            return;
        }

        $task = Task::where('project_id', $this->project->project_id)->findOrFail($taskId);
        $task->update(['status' => $newStatus]);

        $this->project->refresh();
        unset($this->boardColumns);

        Flux::toast(variant: 'success', text: __('Task moved to :status.', ['status' => $newStatus]));
    }

    public function toggleChecklist(string $checklistId): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Update Tasks');

        $checklist = TaskChecklist::whereHas('task', function ($query) {
            $query->where('project_id', $this->project->project_id);
        })->findOrFail($checklistId);

        $checklist->update(['is_completed' => ! $checklist->is_completed]);

        $this->project->refresh();
        unset($this->boardColumns);
    }

    public function deleteTask(string $taskId): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Delete Tasks');

        $task = Task::where('project_id', $this->project->project_id)->findOrFail($taskId);
        $task->delete();

        $this->project->refresh();
        unset($this->boardColumns);

        Flux::toast(variant: 'success', text: __('Task deleted successfully.'));
    }

    // --- Milestone Actions ---

    public function openMilestoneModal(): void
    {
        $this->reset(['milestoneTitle', 'milestoneDescription', 'milestoneDueDate']);
        $this->milestoneStatus = 'Upcoming';
        $this->showMilestoneModal = true;
    }

    public function createMilestone(): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Update Projects');

        $this->validate([
            'milestoneTitle' => ['required', 'string', 'max:255'],
            'milestoneDescription' => ['nullable', 'string'],
            'milestoneStatus' => ['required', 'in:Upcoming,In Progress,Completed'],
            'milestoneDueDate' => ['nullable', 'date'],
        ]);

        $maxOrder = $this->project->milestones()->max('order') ?? 0;

        $this->project->milestones()->create([
            'title' => $this->milestoneTitle,
            'description' => $this->milestoneDescription ?: null,
            'status' => $this->milestoneStatus,
            'due_date' => $this->milestoneDueDate ?: null,
            'order' => $maxOrder + 1,
        ]);

        $this->showMilestoneModal = false;
        $this->reset(['milestoneTitle', 'milestoneDescription', 'milestoneDueDate']);
        $this->project->refresh();
        unset($this->milestones);

        Flux::toast(variant: 'success', text: __('Milestone created successfully.'));
    }

    public function updateMilestoneStatus(string $milestoneId, string $status): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Update Projects');

        if (! in_array($status, ['Upcoming', 'In Progress', 'Completed'])) {
            return;
        }

        $milestone = ProjectMilestone::where('project_id', $this->project->project_id)->findOrFail($milestoneId);
        $milestone->update(['status' => $status]);

        $this->project->refresh();
        unset($this->milestones);

        Flux::toast(variant: 'success', text: __('Milestone status updated.'));
    }

    public function deleteMilestone(string $milestoneId): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Update Projects');

        $milestone = ProjectMilestone::where('project_id', $this->project->project_id)->findOrFail($milestoneId);
        $milestone->delete();

        $this->project->refresh();
        unset($this->milestones);

        Flux::toast(variant: 'success', text: __('Milestone deleted.'));
    }

    // --- Quick Links Modal ---

    public function saveLinks(): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Update Projects');

        $this->validate([
            'linkRepository' => ['nullable', 'url', 'max:255'],
            'linkDocs' => ['nullable', 'url', 'max:255'],
            'linkTesting' => ['nullable', 'url', 'max:255'],
            'linkProd' => ['nullable', 'url', 'max:255'],
        ]);

        $this->project->update([
            'link_repository' => $this->linkRepository ?: null,
            'link_docs' => $this->linkDocs ?: null,
            'link_testing' => $this->linkTesting ?: null,
            'link_prod' => $this->linkProd ?: null,
        ]);

        $this->showLinksModal = false;
        $this->project->refresh();

        Flux::toast(variant: 'success', text: __('Quick links saved successfully.'));
    }

    public static function priorityColor(string $priority): array
    {
        return match ($priority) {
            'High' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-300'],
            'Medium' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-300'],
            'Low' => ['bg' => 'bg-zinc-100 dark:bg-zinc-800', 'text' => 'text-zinc-700 dark:text-zinc-300'],
            default => ['bg' => 'bg-zinc-100 dark:bg-zinc-800', 'text' => 'text-zinc-700 dark:text-zinc-300'],
        };
    }

    public static function healthColor(string $health): array
    {
        return match ($health) {
            'On Track', 'Completed' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-300', 'dot' => 'bg-emerald-500'],
            'At Risk' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-300', 'dot' => 'bg-amber-500'],
            'Delayed' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-300', 'dot' => 'bg-red-500'],
            default => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-300', 'dot' => 'bg-blue-500'],
        };
    }

    public function render(): View
    {
        return view('livewire.projects.show');
    }
}
