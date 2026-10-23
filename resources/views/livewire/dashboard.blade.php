<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <flux:heading size="xl" level="1" class="break-words">
                {{ __('Welcome back, :name', ['name' => $this->currentUser->full_name ?? $this->currentUser->name]) }}
            </flux:heading>

            <div class="mt-3 flex flex-wrap items-center gap-2">
                <flux:badge color="zinc" size="sm">{{ $this->roleLabel }}</flux:badge>
                <flux:text class="text-sm">{{ now()->format('l, F j, Y') }}</flux:text>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @can('IPS - PROJECT - Can List Projects')
                <flux:button size="sm" icon="folder" :href="route('projects.index')" wire:navigate>
                    {{ __('Projects') }}
                </flux:button>
            @endcan

            @can('IPS - JOBTRACKER - Can List Job Tracker')
                <flux:button size="sm" icon="briefcase" :href="route('job-tracker.index')" wire:navigate>
                    {{ __('Job Tracker') }}
                </flux:button>
            @endcan

            @can('PORTFOLIO - Can Manage Portfolio')
                <flux:button size="sm" icon="identification" :href="route('portfolio.profile.edit')" wire:navigate>
                    {{ __('Portfolio') }}
                </flux:button>
            @endcan
        </div>
    </div>

    @if ($this->pendingApprovals !== null)
        <flux:card class="border-amber-200 bg-amber-50 dark:border-amber-900/60 dark:bg-amber-950/30">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-950/60 dark:text-amber-300">
                        <flux:icon name="user-plus" class="size-5" />
                    </div>

                    <div>
                        <flux:heading size="base">{{ __('Access approvals') }}</flux:heading>
                        <flux:text class="mt-1 text-sm">
                            @if ($this->pendingApprovals > 0)
                                {{ trans_choice(':count user is waiting for approval.|:count users are waiting for approval.', $this->pendingApprovals, ['count' => $this->pendingApprovals]) }}
                            @else
                                {{ __('No users are waiting for approval.') }}
                            @endif
                        </flux:text>
                    </div>
                </div>

                <flux:button size="sm" variant="primary" :href="route('approvals.index')" wire:navigate>
                    {{ __('Review approvals') }}
                </flux:button>
            </div>
        </flux:card>
    @endif

    @if (filled($this->projectStats))
        <section class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <flux:heading size="lg">{{ __('Projects overview') }}</flux:heading>
                    <flux:text class="text-sm">{{ __('Projects you own or are a member of.') }}</flux:text>
                </div>

                <flux:button size="sm" variant="ghost" :href="route('projects.index')" wire:navigate>
                    {{ __('View all projects') }}
                </flux:button>
            </div>

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <x-dashboard-stat
                    :label="__('Total projects')"
                    :value="$this->projectStats['total']"
                    icon="folder"
                    tone="zinc"
                />
                <x-dashboard-stat
                    :label="__('In progress')"
                    :value="$this->projectStats['active']"
                    icon="play"
                    tone="blue"
                />
                <x-dashboard-stat
                    :label="__('Completed')"
                    :value="$this->projectStats['completed']"
                    icon="check-circle"
                    tone="emerald"
                />
                <x-dashboard-stat
                    :label="__('Overdue')"
                    :value="$this->projectStats['overdue']"
                    icon="exclamation-triangle"
                    tone="rose"
                />
            </div>
        </section>
    @endif

    @if (filled($this->taskStats))
        <section class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <flux:heading size="lg">{{ __('Tasks overview') }}</flux:heading>
                    <flux:text class="text-sm">{{ __('Open work across projects you can access.') }}</flux:text>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <x-dashboard-stat
                    :label="__('My open tasks')"
                    :value="$this->taskStats['mine_open']"
                    icon="user"
                    tone="purple"
                />
                <x-dashboard-stat
                    :label="__('All open tasks')"
                    :value="$this->taskStats['open']"
                    icon="clipboard-document-list"
                    tone="zinc"
                />
                <x-dashboard-stat
                    :label="__('Due next 7 days')"
                    :value="$this->taskStats['due_soon']"
                    icon="clock"
                    tone="amber"
                />
                <x-dashboard-stat
                    :label="__('Overdue tasks')"
                    :value="$this->taskStats['overdue']"
                    icon="exclamation-triangle"
                    tone="rose"
                />
            </div>
        </section>
    @endif

    @if (filled($this->userStats))
        <section class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <flux:heading size="lg">{{ __('User access') }}</flux:heading>
                    <flux:text class="text-sm">{{ __('Accounts registered in this workspace.') }}</flux:text>
                </div>

                @can('IPS - UAM - Can List Users')
                    <flux:button size="sm" variant="ghost" :href="route('uam.index')" wire:navigate>
                        {{ __('Manage users') }}
                    </flux:button>
                @endcan
            </div>

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-3">
                <x-dashboard-stat
                    :label="__('Total users')"
                    :value="$this->userStats['total']"
                    icon="users"
                    tone="zinc"
                />
                <x-dashboard-stat
                    :label="__('Approved users')"
                    :value="$this->userStats['approved']"
                    icon="check-badge"
                    tone="emerald"
                />
                <x-dashboard-stat
                    :label="__('Pending approval')"
                    :value="$this->userStats['pending']"
                    icon="clock"
                    tone="amber"
                />
            </div>
        </section>
    @endif

    @if (filled($this->jobStats))
        <section class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <flux:heading size="lg">{{ __('Job applications') }}</flux:heading>
                    <flux:text class="text-sm">{{ __('Your personal application pipeline.') }}</flux:text>
                </div>

                <flux:button size="sm" variant="ghost" :href="route('job-tracker.index')" wire:navigate>
                    {{ __('Open job tracker') }}
                </flux:button>
            </div>

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <x-dashboard-stat
                    :label="__('Total applications')"
                    :value="$this->jobStats['total']"
                    icon="briefcase"
                    tone="zinc"
                />
                <x-dashboard-stat
                    :label="__('In interview')"
                    :value="$this->jobStats['interview']"
                    icon="chat-bubble-left-right"
                    tone="blue"
                />
                <x-dashboard-stat
                    :label="__('Offering / accepted')"
                    :value="$this->jobStats['offering']"
                    icon="document-check"
                    tone="emerald"
                />
                <x-dashboard-stat
                    :label="__('Rejected')"
                    :value="$this->jobStats['rejected']"
                    icon="x-circle"
                    tone="rose"
                />
            </div>
        </section>
    @endif

    @if (filled($this->portfolioStats))
        <section class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <flux:heading size="lg">{{ __('Portfolio') }}</flux:heading>
                    <flux:text class="text-sm">{{ __('Keep your public profile up to date.') }}</flux:text>
                </div>

                <flux:button size="sm" variant="ghost" :href="route('portfolio.profile.edit')" wire:navigate>
                    {{ __('Edit portfolio') }}
                </flux:button>
            </div>

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-3">
                <x-dashboard-stat
                    :label="__('Profile')"
                    :value="$this->portfolioStats['profile_completed'] ? __('Complete') : __('Incomplete')"
                    icon="identification"
                    :tone="$this->portfolioStats['profile_completed'] ? 'emerald' : 'amber'"
                />
                <x-dashboard-stat
                    :label="__('Portfolio projects')"
                    :value="$this->portfolioStats['projects']"
                    icon="folder-open"
                    tone="blue"
                />
                <x-dashboard-stat
                    :label="__('Experiences')"
                    :value="$this->portfolioStats['experiences']"
                    icon="building-office"
                    tone="purple"
                />
            </div>
        </section>
    @endif

    @if ($this->upcomingTasks->isNotEmpty() || $this->recentActivity->isNotEmpty())
        <div class="grid gap-4 lg:grid-cols-2">
            @if ($this->upcomingTasks->isNotEmpty())
                <flux:card class="flex flex-col !p-0">
                    <div class="flex items-center justify-between gap-3 border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                        <div>
                            <flux:heading size="base">{{ __('Upcoming deadlines') }}</flux:heading>
                            <flux:text class="text-sm">{{ __('Open tasks with a due date.') }}</flux:text>
                        </div>

                        <flux:button size="sm" variant="ghost" :href="route('projects.index')" wire:navigate>
                            {{ __('View tasks') }}
                        </flux:button>
                    </div>

                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->upcomingTasks as $task)
                            <a
                                href="{{ route('projects.show', $task->project) }}"
                                wire:navigate
                                class="flex items-start justify-between gap-3 px-4 py-3 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/60"
                            >
                                <div class="min-w-0">
                                    <flux:text class="truncate font-medium text-zinc-900 dark:text-zinc-100">{{ $task->title }}</flux:text>
                                    <flux:text class="mt-0.5 truncate text-xs">{{ $task->project?->project_name }}</flux:text>
                                </div>

                                <flux:badge :color="$task->due_date->isPast() ? 'rose' : 'amber'" size="sm" class="shrink-0">
                                    {{ $task->due_date->format('M j') }}
                                </flux:badge>
                            </a>
                        @endforeach
                    </div>
                </flux:card>
            @endif

            @if ($this->recentActivity->isNotEmpty())
                <flux:card class="flex flex-col !p-0">
                    <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                        <flux:heading size="base">{{ __('Recent project activity') }}</flux:heading>
                        <flux:text class="text-sm">{{ __('Latest updates from your projects.') }}</flux:text>
                    </div>

                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->recentActivity as $activity)
                            <div class="flex items-start gap-3 px-4 py-3">
                                <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                    <flux:icon name="bolt" class="size-4" />
                                </div>

                                <div class="min-w-0">
                                    <flux:text class="text-sm text-zinc-900 dark:text-zinc-100">
                                        <span class="font-medium">{{ $activity->user?->name ?? __('System') }}</span>
                                        <span class="text-zinc-400">·</span>
                                        <a href="{{ route('projects.show', $activity->project) }}" wire:navigate class="font-medium underline-offset-2 hover:underline">
                                            {{ $activity->project?->project_name }}
                                        </a>
                                    </flux:text>

                                    <flux:text class="mt-0.5 line-clamp-2 text-xs">
                                        {{ $activity->description ?? str($activity->action)->replace('.', ' ')->title() }}
                                    </flux:text>

                                    <flux:text class="mt-1 text-xs">{{ $activity->created_at?->diffForHumans() }}</flux:text>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </flux:card>
            @endif
        </div>
    @endif

    @if (blank($this->projectStats) && blank($this->taskStats) && blank($this->userStats) && blank($this->jobStats) && blank($this->portfolioStats))
        <flux:card class="flex flex-col items-center justify-center px-6 py-12 text-center">
            <div class="flex size-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                <flux:icon name="lock-closed" class="size-6" />
            </div>

            <flux:heading size="lg" class="mt-4">{{ __('No modules assigned yet') }}</flux:heading>
            <flux:text class="mt-2 max-w-md text-sm">
                {{ __('Your account is approved, but you do not have access to any dashboard module. Contact the owner to request access.') }}
            </flux:text>
        </flux:card>
    @endif
</div>
