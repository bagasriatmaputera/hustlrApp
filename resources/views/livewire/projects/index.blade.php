<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('All Projects') }}</flux:heading>
            <flux:subheading>{{ __('Manage and track all your projects in one place.') }}</flux:subheading>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('projects.create')" wire:navigate>
            {{ __('Add Project') }}
        </flux:button>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.folder class="size-5 text-zinc-600 dark:text-zinc-400" />
                </div>
                <div>
                    <flux:text class="text-xs">{{ __('Total') }}</flux:text>
                    <flux:heading size="xl">{{ $this->totalProjects }}</flux:heading>
                </div>
            </div>
        </flux:card>
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30">
                    <flux:icon.play class="size-5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <flux:text class="text-xs">{{ __('Active') }}</flux:text>
                    <flux:heading size="xl">{{ $this->activeProjects }}</flux:heading>
                </div>
            </div>
        </flux:card>
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                    <flux:icon.check-circle class="size-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <flux:text class="text-xs">{{ __('Completed') }}</flux:text>
                    <flux:heading size="xl">{{ $this->completedProjects }}</flux:heading>
                </div>
            </div>
        </flux:card>
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/30">
                    <flux:icon.pause-circle class="size-5 text-orange-600 dark:text-orange-400" />
                </div>
                <div>
                    <flux:text class="text-xs">{{ __('On Hold') }}</flux:text>
                    <flux:heading size="xl">{{ $this->onHoldProjects }}</flux:heading>
                </div>
            </div>
        </flux:card>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 gap-3">
            <div class="max-w-xs flex-1">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search projects...')" clearable />
            </div>
            <div class="w-48">
                <flux:select wire:model.live="statusFilter" :placeholder="__('All Statuses')">
                    <flux:select.option value="">{{ __('All Statuses') }}</flux:select.option>
                    @foreach ($this->statuses as $status)
                        <flux:select.option :value="$status->status_id">{{ $status->status_name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>
        @if ($search || $statusFilter)
            <flux:button variant="ghost" size="sm" wire:click="clearFilters" icon="x-mark">
                {{ __('Clear filters') }}
            </flux:button>
        @endif
    </div>

    @if ($this->projects->isEmpty())
        <flux:card class="!py-16">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="mb-4 flex size-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.folder-plus class="size-8 text-zinc-400" />
                </div>
                <flux:heading size="lg" class="mb-1">
                    @if ($search || $statusFilter)
                        {{ __('No projects found') }}
                    @else
                        {{ __('No projects yet') }}
                    @endif
                </flux:heading>
                <flux:subheading class="mb-6 max-w-sm">
                    @if ($search || $statusFilter)
                        {{ __('Try adjusting your search or filter to find what you are looking for.') }}
                    @else
                        {{ __('Get started by creating your first project to track and manage your work.') }}
                    @endif
                </flux:subheading>
                @if ($search || $statusFilter)
                    <flux:button variant="primary" wire:click="clearFilters" icon="arrow-path">
                        {{ __('Clear filters') }}
                    </flux:button>
                @else
                    <flux:button variant="primary" icon="plus" :href="route('projects.create')" wire:navigate>
                        {{ __('Create Your First Project') }}
                    </flux:button>
                @endif
            </div>
        </flux:card>
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($this->projects as $project)
                @php
                    $colors = \App\Livewire\Projects\Index::statusColor($project->status->status_name);
                @endphp
                <flux:card wire:key="project-{{ $project->project_id }}" class="group relative flex flex-col gap-4 transition-shadow hover:shadow-md dark:hover:shadow-zinc-700/30">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex min-w-0 flex-1 flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <span class="{{ $colors['bg'] }} {{ $colors['text'] }} inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                                    {{ $project->status->status_name }}
                                </span>
                                @if ($project->isRecentlyUpdated())
                                    <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                                        {{ __('Updated') }}
                                    </span>
                                @endif
                            </div>
                            <flux:heading size="lg" class="truncate">{{ $project->project_name }}</flux:heading>
                        </div>
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" class="opacity-0 transition-opacity group-hover:opacity-100" />
                            <flux:menu>
                                <flux:menu.item icon="pencil-square" :href="route('projects.edit', $project->project_id)" wire:navigate>
                                    {{ __('Edit') }}
                                </flux:menu.item>
                                @if ($project->link_testing)
                                    <flux:menu.item icon="globe-alt" :href="$project->link_testing" target="_blank">
                                        {{ __('Testing Link') }}
                                    </flux:menu.item>
                                @endif
                                @if ($project->link_prod)
                                    <flux:menu.item icon="link" :href="$project->link_prod" target="_blank">
                                        {{ __('Production Link') }}
                                    </flux:menu.item>
                                @endif
                                <flux:menu.separator />
                                <flux:menu.item icon="trash" variant="danger" wire:click="deleteProject('{{ $project->project_id }}')" wire:confirm="{{ __('Are you sure you want to delete this project?') }}">
                                    {{ __('Delete') }}
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    @if ($project->description)
                        <flux:text class="line-clamp-2 text-sm">{{ $project->description }}</flux:text>
                    @endif

                    @if ($project->client)
                        <div class="flex items-center gap-2">
                            <flux:icon.building-office-2 class="size-4 text-zinc-400" />
                            <flux:text class="text-sm">{{ $project->client->client_name }}</flux:text>
                        </div>
                    @endif

                    <div class="flex items-center gap-2">
                        <flux:icon.calendar class="size-4 text-zinc-400" />
                        <flux:text class="text-sm">
                            {{ $project->start_date?->format('M d, Y') ?? '-' }}
                            <span class="text-zinc-400">→</span>
                            {{ $project->end_date?->format('M d, Y') ?? __('Ongoing') }}
                        </flux:text>
                    </div>

                    @if ($project->technologies->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($project->technologies->take(4) as $tech)
                                <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                    {{ $tech->tech_name }}
                                </span>
                            @endforeach
                            @if ($project->technologies->count() > 4)
                                <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                    +{{ $project->technologies->count() - 4 }}
                                </span>
                            @endif
                        </div>
                    @endif

                    <flux:separator />

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                @foreach ($project->members->take(3) as $member)
                                    <flux:avatar size="xs" :name="$member->full_name ?? $member->name ?? 'U'" class="ring-2 ring-white dark:ring-zinc-900" />
                                @endforeach
                            </div>
                            @if ($project->members->count() > 3)
                                <flux:text class="text-xs">+{{ $project->members->count() - 3 }}</flux:text>
                            @endif
                        </div>
                        <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                            {{ $project->ownership_type }}
                        </span>
                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="mt-2">
            {{ $this->projects->links() }}
        </div>
    @endif
</div>
