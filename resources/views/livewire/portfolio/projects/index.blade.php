<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Portfolio Projects') }}</flux:heading>
            <flux:subheading>{{ __('Manage your portfolio projects.') }}</flux:subheading>
        </div>
        @can('PORTFOLIO - Can Manage Projects')
            <flux:button variant="primary" icon="plus" :href="route('portfolio.projects.create')" wire:navigate class="w-full sm:w-auto">
                {{ __('Add Project') }}
            </flux:button>
        @endcan
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <div class="w-full sm:w-80">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search projects...')" clearable />
        </div>
    </div>

    @if ($this->projects->isEmpty())
        <flux:card class="!py-16">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="mb-4 flex size-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.folder-plus class="size-8 text-zinc-400" />
                </div>
                <flux:heading size="lg" class="mb-1">{{ __('No portfolio projects yet') }}</flux:heading>
                <flux:subheading class="mb-6 max-w-sm">{{ __('Add your first portfolio project to showcase your work.') }}</flux:subheading>
                @can('PORTFOLIO - Can Manage Projects')
                    <flux:button variant="primary" icon="plus" :href="route('portfolio.projects.create')" wire:navigate>
                        {{ __('Add First Project') }}
                    </flux:button>
                @endcan
            </div>
        </flux:card>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($this->projects as $project)
                <flux:card wire:key="portfolio-project-{{ $project->project_id }}" class="group relative flex flex-col gap-4 transition-shadow hover:shadow-md dark:hover:shadow-zinc-700/30">
                    {{-- Cover Photo Thumbnail Preview --}}
                    @if ($project->image)
                        @php
                            $cardImageUrl = filter_var($project->image, FILTER_VALIDATE_URL)
                                ? $project->image
                                : (str_starts_with($project->image, '/') || str_starts_with($project->image, 'storage/')
                                    ? asset(ltrim($project->image, '/'))
                                    : (str_starts_with($project->image, 'portfolio/')
                                        ? route('portfolio.media', ['path' => $project->image])
                                        : Storage::disk((string) config('filesystems.default', 's3'))->url($project->image)));
                        @endphp
                        <div class="aspect-video w-full overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700/60">
                            <img src="{{ $cardImageUrl }}" alt="{{ $project->title['en'] ?? '' }}" class="h-full w-full object-cover object-top transition-transform duration-300 group-hover:scale-105" loading="lazy" />
                        </div>
                    @endif

                    <div class="flex items-start justify-between gap-2">
                        <div class="flex min-w-0 flex-1 flex-col gap-1">
                            <div class="flex flex-wrap gap-1.5">
                                @if ($project->featured)
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                        {{ __('Featured') }}
                                    </span>
                                @endif
                                @if ($project->is_live)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                        {{ __('Live') }}
                                    </span>
                                @endif
                                @if ($project->is_repo)
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ __('Repo') }}
                                    </span>
                                @endif
                            </div>
                            <flux:heading size="lg" class="truncate" title="{{ $project->title['en'] ?? '-' }}">{{ $project->title['en'] ?? '-' }}</flux:heading>
                            <flux:text class="text-xs text-zinc-500 truncate">{{ $project->title['id'] ?? '' }}</flux:text>
                        </div>
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" class="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 shrink-0" />
                            <flux:menu>
                                <flux:menu.item icon="pencil-square" :href="route('portfolio.projects.edit', $project->project_id)" wire:navigate>
                                    {{ __('Edit') }}
                                </flux:menu.item>
                                @if ($project->live_url)
                                    <flux:menu.item icon="globe-alt" :href="$project->live_url" target="_blank">
                                        {{ __('Live URL') }}
                                    </flux:menu.item>
                                @endif
                                @if ($project->repo_url)
                                    <flux:menu.item icon="code-bracket" :href="$project->repo_url" target="_blank">
                                        {{ __('Repo URL') }}
                                    </flux:menu.item>
                                @endif
                                <flux:menu.separator />
                                <flux:menu.item icon="trash" variant="danger" wire:click="delete('{{ $project->project_id }}')" wire:confirm="{{ __('Are you sure you want to delete this project?') }}">
                                    {{ __('Delete') }}
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <flux:text class="line-clamp-2 text-sm">{{ $project->description['en'] ?? '' }}</flux:text>

                    @if (!empty($project->tech_stack))
                        <div class="flex flex-wrap gap-1.5">
                            @foreach (array_slice($project->tech_stack, 0, 5) as $tech)
                                <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                    {{ $tech }}
                                </span>
                            @endforeach
                            @if (count($project->tech_stack) > 5)
                                <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                    +{{ count($project->tech_stack) - 5 }}
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-xs text-zinc-400 pt-1 border-t border-zinc-100 dark:border-zinc-800">
                        <span>{{ __('Order:') }} {{ $project->order }}</span>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif
</div>
