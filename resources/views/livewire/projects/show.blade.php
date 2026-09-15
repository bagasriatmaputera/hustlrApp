<div class="flex h-full w-full flex-1 flex-col gap-6">
    @php
        $statusColors = \App\Livewire\Projects\Index::statusColor($project->status->status_name);
        $healthColors = \App\Livewire\Projects\Show::healthColor($project->health_status ?? 'On Track');
        $progress = $project->taskProgressPercentage();
        $daysLeft = $project->daysRemaining();
    @endphp

    {{-- Breadcrumbs & Header Actions --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-zinc-500">
            <a href="{{ route('projects.index') }}" class="hover:text-zinc-800 dark:hover:text-zinc-200" wire:navigate>
                {{ __('Projects') }}
            </a>
            <span>/</span>
            <span class="font-medium text-zinc-900 dark:text-zinc-100 truncate max-w-[200px] sm:max-w-md">
                {{ $project->project_name }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <flux:button variant="ghost" size="sm" icon="pencil-square" :href="route('projects.edit', $project)" wire:navigate>
                {{ __('Edit Project') }}
            </flux:button>
            <flux:button variant="ghost" size="sm" icon="link" wire:click="$set('showLinksModal', true)">
                {{ __('Edit Links') }}
            </flux:button>
        </div>
    </div>

    {{-- 1. Project Header & Overview Strip --}}
    <flux:card class="!p-6 flex flex-col gap-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex flex-col gap-2 max-w-2xl">
                {{-- Status & Health Badges --}}
                <div class="flex flex-wrap items-center gap-2">
                    <span class="{{ $statusColors['bg'] }} {{ $statusColors['text'] }} inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold">
                        {{ $project->status->status_name }}
                    </span>

                    {{-- Health Badge with interactive dropdown --}}
                    <flux:dropdown position="bottom" align="start">
                        <button type="button" class="{{ $healthColors['bg'] }} {{ $healthColors['text'] }} inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold hover:opacity-80 transition cursor-pointer">
                            <span class="size-1.5 rounded-full {{ $healthColors['dot'] }}"></span>
                            <span>{{ $project->health_status ?? 'On Track' }}</span>
                            <flux:icon.chevron-down class="size-3 opacity-60" />
                        </button>
                        <flux:menu>
                            <flux:menu.item wire:click="updateHealthStatus('On Track')">
                                <span class="size-2 rounded-full bg-emerald-500 mr-2 inline-block"></span> {{ __('On Track') }}
                            </flux:menu.item>
                            <flux:menu.item wire:click="updateHealthStatus('At Risk')">
                                <span class="size-2 rounded-full bg-amber-500 mr-2 inline-block"></span> {{ __('At Risk') }}
                            </flux:menu.item>
                            <flux:menu.item wire:click="updateHealthStatus('Delayed')">
                                <span class="size-2 rounded-full bg-red-500 mr-2 inline-block"></span> {{ __('Delayed') }}
                            </flux:menu.item>
                            <flux:menu.item wire:click="updateHealthStatus('Completed')">
                                <span class="size-2 rounded-full bg-blue-500 mr-2 inline-block"></span> {{ __('Completed') }}
                            </flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>

                    <span class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 text-xs text-zinc-600 dark:text-zinc-400">
                        {{ $project->ownership_type }}
                    </span>

                    @if ($project->client)
                        <span class="inline-flex items-center gap-1 text-xs text-zinc-500">
                            <flux:icon.building-office-2 class="size-3.5" />
                            {{ $project->client->client_name }}
                        </span>
                    @endif
                </div>

                {{-- Title & Description --}}
                <flux:heading size="xl" level="1">{{ $project->project_name }}</flux:heading>
                @if ($project->description)
                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2">
                        {{ $project->description }}
                    </flux:text>
                @endif
            </div>

            {{-- Quick Action Buttons / Links --}}
            <div class="flex flex-wrap items-center gap-2">
                @if ($project->link_repository)
                    <flux:button variant="ghost" size="sm" icon="code-bracket" :href="$project->link_repository" target="_blank">
                        {{ __('GitHub Repo') }}
                    </flux:button>
                @else
                    <flux:button variant="ghost" size="sm" icon="code-bracket" wire:click="$set('showLinksModal', true)" class="opacity-60">
                        + {{ __('Add Repo') }}
                    </flux:button>
                @endif

                @if ($project->link_testing || $project->link_prod)
                    <flux:button variant="ghost" size="sm" icon="globe-alt" :href="$project->link_prod ?? $project->link_testing" target="_blank">
                        {{ __('Live Demo') }}
                    </flux:button>
                @endif

                @if ($project->link_docs)
                    <flux:button variant="ghost" size="sm" icon="document-text" :href="$project->link_docs" target="_blank">
                        {{ __('Figma / Docs') }}
                    </flux:button>
                @else
                    <flux:button variant="ghost" size="sm" icon="document-text" wire:click="$set('showLinksModal', true)" class="opacity-60">
                        + {{ __('Add Docs') }}
                    </flux:button>
                @endif
            </div>
        </div>

        <flux:separator />

        {{-- Quick Metrics Strip --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            {{-- Metric 1: Task Completion Progress --}}
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-medium text-zinc-600 dark:text-zinc-400">{{ __('Task Completion') }}</span>
                    <span class="font-bold text-zinc-900 dark:text-zinc-100">
                        {{ $project->completedTasksCount() }}/{{ $project->totalTasksCount() }} ({{ $progress }}%)
                    </span>
                </div>
                <div class="h-2.5 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <div class="h-full rounded-full bg-primary transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>
                <flux:text class="text-xs text-zinc-500">
                    {{ $project->totalTasksCount() - $project->completedTasksCount() }} {{ __('tasks remaining') }}
                </flux:text>
            </div>

            {{-- Metric 2: Target Release Date --}}
            <div class="flex flex-col gap-1 border-t sm:border-t-0 sm:border-l border-zinc-100 dark:border-zinc-800 pt-4 sm:pt-0 sm:pl-6">
                <span class="text-xs font-medium text-zinc-500">{{ __('Target Release / Deadline') }}</span>
                <div class="flex items-center gap-2 mt-0.5">
                    <flux:icon.calendar class="size-4 text-zinc-400" />
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $project->end_date?->format('M d, Y') ?? __('No deadline set') }}
                    </span>
                </div>
                <flux:text class="text-xs text-zinc-500">
                    {{ __('Started on') }} {{ $project->start_date?->format('M d, Y') ?? '—' }}
                </flux:text>
            </div>

            {{-- Metric 3: Time Remaining / Estimate --}}
            <div class="flex flex-col gap-1 border-t sm:border-t-0 sm:border-l border-zinc-100 dark:border-zinc-800 pt-4 sm:pt-0 sm:pl-6">
                <span class="text-xs font-medium text-zinc-500">{{ __('Timeline Status') }}</span>
                <div class="flex items-center gap-2 mt-0.5">
                    <flux:icon.clock class="size-4 text-zinc-400" />
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">
                        @if ($daysLeft !== null)
                            @if ($daysLeft > 0)
                                <span class="text-emerald-600 dark:text-emerald-400">{{ $daysLeft }} {{ __('days remaining') }}</span>
                            @elseif ($daysLeft === 0)
                                <span class="text-amber-600 dark:text-amber-400">{{ __('Due today') }}</span>
                            @else
                                <span class="text-red-600 dark:text-red-400">{{ abs($daysLeft) }} {{ __('days overdue') }}</span>
                            @endif
                        @else
                            {{ __('Continuous / Open') }}
                        @endif
                    </span>
                </div>
                <flux:text class="text-xs text-zinc-500">
                    {{ $project->status->status_name }} ({{ $project->ownership_type }})
                </flux:text>
            </div>
        </div>
    </flux:card>

    {{-- 2. Tab Navigation Strip --}}
    <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-2">
        <div class="flex gap-4">
            <button
                type="button"
                wire:click="setTab('board')"
                class="flex items-center gap-2 pb-2 text-sm font-semibold transition border-b-2 {{ $activeTab === 'board' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200' }}"
            >
                <flux:icon.view-columns class="size-4" />
                <span>{{ __('Board View') }}</span>
                <span class="rounded-full bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 text-xs text-zinc-600 dark:text-zinc-400">
                    {{ $project->totalTasksCount() }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setTab('roadmap')"
                class="flex items-center gap-2 pb-2 text-sm font-semibold transition border-b-2 {{ $activeTab === 'roadmap' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200' }}"
            >
                <flux:icon.map class="size-4" />
                <span>{{ __('Roadmap / Milestones') }}</span>
                <span class="rounded-full bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 text-xs text-zinc-600 dark:text-zinc-400">
                    {{ $this->milestones->count() }}
                </span>
            </button>
        </div>

        <div>
            @if ($activeTab === 'board')
                <flux:button variant="primary" size="sm" icon="plus" wire:click="openTaskModal('Backlog')">
                    {{ __('Add Task') }}
                </flux:button>
            @else
                <flux:button variant="primary" size="sm" icon="plus" wire:click="openMilestoneModal">
                    {{ __('Add Milestone') }}
                </flux:button>
            @endif
        </div>
    </div>

    {{-- TAB 1: BOARD VIEW (KANBAN) --}}
    @if ($activeTab === 'board')
        @php
            $columns = [
                'Backlog' => [
                    'label' => __('Backlog / Not Started'),
                    'dot' => 'bg-zinc-400',
                    'bg' => 'bg-zinc-50/50 dark:bg-zinc-900/30',
                ],
                'In Progress' => [
                    'label' => __('In Progress'),
                    'dot' => 'bg-amber-500',
                    'bg' => 'bg-amber-50/20 dark:bg-amber-950/10',
                ],
                'In Review' => [
                    'label' => __('Code Review / Testing'),
                    'dot' => 'bg-purple-500',
                    'bg' => 'bg-purple-50/20 dark:bg-purple-950/10',
                ],
                'Done' => [
                    'label' => __('Done'),
                    'dot' => 'bg-emerald-500',
                    'bg' => 'bg-emerald-50/20 dark:bg-emerald-950/10',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 items-start">
            @foreach ($columns as $statusKey => $colMeta)
                @php
                    $tasks = $this->boardColumns[$statusKey] ?? collect();
                @endphp

                <div class="flex flex-col gap-3 rounded-xl border border-zinc-200 dark:border-zinc-800 p-3 {{ $colMeta['bg'] }}">
                    {{-- Column Header --}}
                    <div class="flex items-center justify-between px-1">
                        <div class="flex items-center gap-2">
                            <span class="size-2 rounded-full {{ $colMeta['dot'] }}"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300">
                                {{ $colMeta['label'] }}
                            </span>
                            <span class="rounded-full bg-zinc-200 dark:bg-zinc-700 px-1.5 py-0.2 text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">
                                {{ $tasks->count() }}
                            </span>
                        </div>
                        <flux:button variant="ghost" size="xs" icon="plus" wire:click="openTaskModal('{{ $statusKey }}')" />
                    </div>

                    {{-- Task Cards List --}}
                    <div class="flex flex-col gap-2.5 min-h-[140px]">
                        @forelse ($tasks as $task)
                            @php
                                $priorityStyles = \App\Livewire\Projects\Show::priorityColor($task->priority);
                                $subtaskCount = $task->checklists->count();
                                $completedSubtasks = $task->checklists->where('is_completed', true)->count();
                            @endphp

                            <flux:card wire:key="task-card-{{ $task->task_id }}" class="!p-3 flex flex-col gap-2.5 hover:shadow-sm transition-all border border-zinc-200/80 dark:border-zinc-800">
                                {{-- Card Badges & Options --}}
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="{{ $priorityStyles['bg'] }} {{ $priorityStyles['text'] }} rounded px-1.5 py-0.5 text-[10px] font-semibold">
                                            {{ $task->priority }}
                                        </span>
                                        @if ($task->category)
                                            <span class="rounded bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 text-[10px] font-medium text-zinc-600 dark:text-zinc-400">
                                                {{ $task->category }}
                                            </span>
                                        @endif
                                    </div>

                                    <flux:dropdown position="bottom" align="end">
                                        <flux:button variant="ghost" size="xs" icon="ellipsis-horizontal" />
                                        <flux:menu>
                                            <flux:menu.submenu heading="{{ __('Move to...') }}">
                                                @foreach (['Backlog', 'In Progress', 'In Review', 'Done'] as $opt)
                                                    @if ($opt !== $task->status)
                                                        <flux:menu.item wire:click="updateTaskStatus('{{ $task->task_id }}', '{{ $opt }}')">
                                                            {{ $opt }}
                                                        </flux:menu.item>
                                                    @endif
                                                @endforeach
                                            </flux:menu.submenu>
                                            <flux:menu.separator />
                                            <flux:menu.item variant="danger" icon="trash" wire:click="deleteTask('{{ $task->task_id }}')">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>

                                {{-- Title & Description --}}
                                <div>
                                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $task->title }}
                                    </div>
                                    @if ($task->description)
                                        <div class="text-xs text-zinc-500 mt-1 line-clamp-2">
                                            {{ $task->description }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Subtask Checklist --}}
                                @if ($subtaskCount > 0)
                                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800/50 p-2 flex flex-col gap-1.5 mt-1">
                                        <div class="flex items-center justify-between text-[11px] text-zinc-500 font-medium">
                                            <span>{{ __('Sub-tasks') }}</span>
                                            <span>{{ $completedSubtasks }}/{{ $subtaskCount }}</span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            @foreach ($task->checklists as $chk)
                                                <label wire:key="chk-{{ $chk->checklist_id }}" class="flex items-center gap-2 text-xs cursor-pointer group">
                                                    <input
                                                        type="checkbox"
                                                        wire:click="toggleChecklist('{{ $chk->checklist_id }}')"
                                                        {{ $chk->is_completed ? 'checked' : '' }}
                                                        class="rounded border-zinc-300 text-primary focus:ring-primary size-3.5"
                                                    />
                                                    <span class="{{ $chk->is_completed ? 'line-through text-zinc-400 dark:text-zinc-500' : 'text-zinc-700 dark:text-zinc-300' }}">
                                                        {{ $chk->title }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Footer: Due Date & Assignee --}}
                                <div class="flex items-center justify-between text-[11px] text-zinc-400 mt-1 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
                                    <div class="flex items-center gap-1">
                                        <flux:icon.calendar class="size-3 text-zinc-400" />
                                        <span>{{ $task->due_date?->format('M d') ?? __('No date') }}</span>
                                    </div>
                                    @if ($subtaskCount > 0)
                                        <div class="flex items-center gap-1">
                                            <flux:icon.check-circle class="size-3 {{ $completedSubtasks === $subtaskCount ? 'text-emerald-500' : 'text-zinc-400' }}" />
                                            <span>{{ $completedSubtasks }}/{{ $subtaskCount }}</span>
                                        </div>
                                    @endif
                                </div>
                            </flux:card>
                        @empty
                            <div class="flex flex-col items-center justify-center p-6 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg">
                                <span class="text-xs text-zinc-400">{{ __('No tasks yet') }}</span>
                                <button type="button" wire:click="openTaskModal('{{ $statusKey }}')" class="mt-1 text-xs text-primary font-medium hover:underline">
                                    + {{ __('Add task') }}
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- TAB 2: ROADMAP / MILESTONES (LINIMASA) --}}
    @if ($activeTab === 'roadmap')
        <div class="flex flex-col gap-6 mx-auto w-full">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">{{ __('Project Roadmap & Release Sprints') }}</flux:heading>
                    <flux:subheading>{{ __('Key stages and delivery milestones across the project lifecycle.') }}</flux:subheading>
                </div>
                <flux:button variant="primary" size="sm" icon="plus" wire:click="openMilestoneModal">
                    {{ __('New Milestone') }}
                </flux:button>
            </div>

            @if ($this->milestones->isEmpty())
                <flux:card class="!py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="mb-4 flex size-14 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <flux:icon.map class="size-7 text-zinc-400" />
                        </div>
                        <flux:heading size="md">{{ __('No milestones defined yet') }}</flux:heading>
                        <flux:subheading class="max-w-sm mb-4">
                            {{ __('Break your project into strategic phases such as Sprint 1 (Auth), Sprint 2 (Payments), and Sprint 3 (Launch).') }}
                        </flux:subheading>
                        <flux:button variant="primary" size="sm" icon="plus" wire:click="openMilestoneModal">
                            {{ __('Create First Milestone') }}
                        </flux:button>
                    </div>
                </flux:card>
            @else
                <div class="relative pl-6 border-l-2 border-zinc-200 dark:border-zinc-800 flex flex-col gap-8 ml-3">
                    @foreach ($this->milestones as $m)
                        @php
                            $mColors = match ($m->status) {
                                'Completed' => ['badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300', 'dot' => 'bg-emerald-500 ring-emerald-100 dark:ring-emerald-950'],
                                'In Progress' => ['badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300', 'dot' => 'bg-amber-500 ring-amber-100 dark:ring-amber-950'],
                                default => ['badge' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300', 'dot' => 'bg-zinc-400 ring-zinc-100 dark:ring-zinc-800'],
                            };
                        @endphp

                        <div wire:key="milestone-{{ $m->milestone_id }}" class="relative group">
                            {{-- Timeline Node Dot --}}
                            <div class="absolute -left-[31px] top-1.5 size-4 rounded-full {{ $mColors['dot'] }} ring-4"></div>

                            <flux:card class="!p-5 flex flex-col gap-3 transition-shadow hover:shadow-md">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <flux:dropdown position="bottom" align="start">
                                                <button type="button" class="{{ $mColors['badge'] }} inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold hover:opacity-80 transition cursor-pointer">
                                                    <span>{{ $m->status }}</span>
                                                    <flux:icon.chevron-down class="size-3 opacity-60" />
                                                </button>
                                                <flux:menu>
                                                    <flux:menu.item wire:click="updateMilestoneStatus('{{ $m->milestone_id }}', 'Upcoming')">
                                                        {{ __('Upcoming') }}
                                                    </flux:menu.item>
                                                    <flux:menu.item wire:click="updateMilestoneStatus('{{ $m->milestone_id }}', 'In Progress')">
                                                        {{ __('In Progress') }}
                                                    </flux:menu.item>
                                                    <flux:menu.item wire:click="updateMilestoneStatus('{{ $m->milestone_id }}', 'Completed')">
                                                        {{ __('Completed') }}
                                                    </flux:menu.item>
                                                </flux:menu>
                                            </flux:dropdown>

                                            @if ($m->due_date)
                                                <span class="text-xs text-zinc-400 flex items-center gap-1">
                                                    <flux:icon.calendar class="size-3.5" />
                                                    {{ $m->due_date->format('M d, Y') }}
                                                </span>
                                            @endif
                                        </div>

                                        <flux:heading size="lg" class="mt-1">{{ $m->title }}</flux:heading>
                                    </div>

                                    <flux:button variant="ghost" size="xs" icon="trash" wire:click="deleteMilestone('{{ $m->milestone_id }}')" wire:confirm="{{ __('Delete milestone?') }}" class="text-zinc-400 hover:text-red-500" />
                                </div>

                                @if ($m->description)
                                    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $m->description }}
                                    </flux:text>
                                @endif
                            </flux:card>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- MODAL: CREATE TASK --}}
    <flux:modal wire:model="showTaskModal" class="md:w-xl">
        <form wire:submit="createTask" class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('Create Task') }}</flux:heading>
                <flux:subheading>{{ __('Add a new action item to this project board.') }}</flux:subheading>
            </div>

            <flux:input wire:model="taskTitle" :label="__('Task Title')" placeholder="e.g. Implement Fortify 2FA Authentication" required />

            <flux:textarea wire:model="taskDescription" :label="__('Description (Optional)')" placeholder="Details or context for this task..." rows="2" />

            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="taskStatus" :label="__('Column Status')">
                    <flux:select.option value="Backlog">{{ __('Backlog / Not Started') }}</flux:select.option>
                    <flux:select.option value="In Progress">{{ __('In Progress') }}</flux:select.option>
                    <flux:select.option value="In Review">{{ __('Code Review / Testing') }}</flux:select.option>
                    <flux:select.option value="Done">{{ __('Done') }}</flux:select.option>
                </flux:select>

                <flux:select wire:model="taskPriority" :label="__('Priority')">
                    <flux:select.option value="Low">{{ __('Low') }}</flux:select.option>
                    <flux:select.option value="Medium">{{ __('Medium') }}</flux:select.option>
                    <flux:select.option value="High">{{ __('High') }}</flux:select.option>
                </flux:select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="taskCategory" :label="__('Technology / Category Tag')">
                    <flux:select.option value="Backend">{{ __('Backend') }}</flux:select.option>
                    <flux:select.option value="UI">{{ __('UI / Frontend') }}</flux:select.option>
                    <flux:select.option value="Bug">{{ __('Bug Fix') }}</flux:select.option>
                    <flux:select.option value="Database">{{ __('Database') }}</flux:select.option>
                    <flux:select.option value="DevOps">{{ __('DevOps / Infra') }}</flux:select.option>
                </flux:select>

                <flux:input wire:model="taskDueDate" type="date" :label="__('Due Date (Optional)')" />
            </div>

            {{-- Subtasks Checklist Builder --}}
            <div class="space-y-2">
                <flux:label>{{ __('Sub-tasks / Checklist (Optional)') }}</flux:label>
                <div class="flex gap-2">
                    <flux:input wire:model="tempSubtask" placeholder="e.g. Write feature test" class="flex-1" wire:keydown.enter.prevent="addSubtaskItem" />
                    <flux:button type="button" size="sm" wire:click="addSubtaskItem">{{ __('Add') }}</flux:button>
                </div>
                @if (!empty($newSubtasks))
                    <div class="mt-2 space-y-1 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 p-2.5">
                        @foreach ($newSubtasks as $idx => $st)
                            <div class="flex items-center justify-between text-xs text-zinc-700 dark:text-zinc-300">
                                <span>• {{ $st }}</span>
                                <button type="button" wire:click="removeSubtaskItem({{ $idx }})" class="text-zinc-400 hover:text-red-500">
                                    <flux:icon.x-mark class="size-3.5" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create Task') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- MODAL: CREATE MILESTONE --}}
    <flux:modal wire:model="showMilestoneModal" class="md:w-lg">
        <form wire:submit="createMilestone" class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('Create Milestone / Sprint') }}</flux:heading>
                <flux:subheading>{{ __('Define a major delivery phase for this project roadmap.') }}</flux:subheading>
            </div>

            <flux:input wire:model="milestoneTitle" :label="__('Milestone Title')" placeholder="e.g. Sprint 1: Setup Database & Autentikasi" required />

            <flux:textarea wire:model="milestoneDescription" :label="__('Scope / Description')" placeholder="Briefly explain deliverables in this milestone..." rows="3" />

            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="milestoneStatus" :label="__('Status')">
                    <flux:select.option value="Upcoming">{{ __('Upcoming') }}</flux:select.option>
                    <flux:select.option value="In Progress">{{ __('In Progress') }}</flux:select.option>
                    <flux:select.option value="Completed">{{ __('Completed') }}</flux:select.option>
                </flux:select>

                <flux:input wire:model="milestoneDueDate" type="date" :label="__('Target Delivery Date')" />
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create Milestone') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- MODAL: EDIT QUICK LINKS --}}
    <flux:modal wire:model="showLinksModal" class="md:w-lg">
        <form wire:submit="saveLinks" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('Project Quick Links') }}</flux:heading>
                <flux:subheading>{{ __('Connect relevant repositories, environments, and documents.') }}</flux:subheading>
            </div>

            <flux:input wire:model="linkRepository" type="url" :label="__('GitHub / Repository URL')" placeholder="https://github.com/..." icon="code-bracket" />
            <flux:input wire:model="linkDocs" type="url" :label="__('Figma / Documentation URL')" placeholder="https://figma.com/... or https://notion.so/..." icon="document-text" />
            <flux:input wire:model="linkTesting" type="url" :label="__('Staging / Testing URL')" placeholder="https://staging.example.com" icon="beaker" />
            <flux:input wire:model="linkProd" type="url" :label="__('Production URL')" placeholder="https://example.com" icon="globe-alt" />

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save Links') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
