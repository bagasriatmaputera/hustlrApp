<div class="flex h-full w-full flex-1 flex-col gap-6">
    {{-- Header Section --}}
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Technologies & Tech Stack') }}</flux:heading>
            <flux:subheading>{{ __('Manage programming languages, frameworks, databases, and tools used across your projects.') }}</flux:subheading>
        </div>
        <div>
            <flux:button variant="primary" icon="plus" wire:click="openCreateModal">
                {{ __('Add Technology') }}
            </flux:button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400">
                    <flux:icon.cpu-chip class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs">{{ __('Total Technologies') }}</flux:text>
                    <flux:heading size="xl">{{ $this->totalTechnologies }}</flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                    <flux:icon.squares-2x2 class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs">{{ __('Categories') }}</flux:text>
                    <flux:heading size="xl">{{ $this->totalCategories }}</flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card class="!p-4 col-span-2 lg:col-span-1">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <flux:icon.folder-git-2 class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs">{{ __('Active Filter') }}</flux:text>
                    <flux:heading size="lg" class="truncate">
                        {{ $categoryFilter ?: __('All Categories') }}
                    </flux:heading>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Search and Filter Toolbar --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 flex-wrap gap-3">
            <div class="w-full sm:w-72">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search technology name...')" clearable />
            </div>

            <div class="w-full sm:w-56">
                <flux:select wire:model.live="categoryFilter" :placeholder="__('All Categories')">
                    <flux:select.option value="">{{ __('All Categories') }}</flux:select.option>
                    @foreach ($this->categories as $cat)
                        <flux:select.option :value="$cat">{{ $cat }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        @if ($search || $categoryFilter)
            <flux:button variant="ghost" size="sm" wire:click="clearFilters" icon="x-mark">
                {{ __('Clear filters') }}
            </flux:button>
        @endif
    </div>

    {{-- Technologies Table / List --}}
    @if ($this->technologies->isEmpty())
        <flux:card class="!py-16">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="mb-4 flex size-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.cpu-chip class="size-8 text-zinc-400" />
                </div>
                <flux:heading size="lg" class="mb-1">
                    @if ($search || $categoryFilter)
                        {{ __('No technologies match your filter') }}
                    @else
                        {{ __('No technologies added yet') }}
                    @endif
                </flux:heading>
                <flux:subheading class="mb-6 max-w-sm">
                    @if ($search || $categoryFilter)
                        {{ __('Try adjusting your search query or clear the category filter.') }}
                    @else
                        {{ __('Start by adding programming languages, frameworks, and tools to your tech stack.') }}
                    @endif
                </flux:subheading>
                @if ($search || $categoryFilter)
                    <flux:button variant="ghost" size="sm" wire:click="clearFilters">
                        {{ __('Reset Filters') }}
                    </flux:button>
                @else
                    <flux:button variant="primary" icon="plus" wire:click="openCreateModal">
                        {{ __('Add First Technology') }}
                    </flux:button>
                @endif
            </div>
        </flux:card>
    @else
        <flux:card class="!p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-zinc-200 bg-zinc-50/50 text-xs font-semibold text-zinc-600 uppercase tracking-wider dark:border-zinc-800 dark:bg-zinc-900/50 dark:text-zinc-400">
                        <tr>
                            <th scope="col" class="py-3.5 ps-4 pe-3 sm:ps-6">{{ __('Technology Name') }}</th>
                            <th scope="col" class="px-3 py-3.5">{{ __('Category') }}</th>
                            <th scope="col" class="px-3 py-3.5 text-center">{{ __('Linked Projects') }}</th>
                            <th scope="col" class="px-3 py-3.5">{{ __('Date Added') }}</th>
                            <th scope="col" class="relative py-3.5 ps-3 pe-4 text-right sm:pe-6">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach ($this->technologies as $tech)
                            @php
                                $badgeStyle = \App\Livewire\Technologies\Index::categoryBadge($tech->category);
                            @endphp
                            <tr class="transition hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40">
                                <td class="whitespace-nowrap py-4 ps-4 pe-3 sm:ps-6">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 font-bold text-xs text-zinc-700 dark:text-zinc-300 shadow-xs">
                                            {{ substr($tech->tech_name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                                {{ $tech->tech_name }}
                                            </div>
                                            <div class="text-[11px] text-zinc-400">
                                                ID: {{ substr($tech->tech_id, 0, 8) }}...
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-3 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeStyle['bg'] }} {{ $badgeStyle['text'] }}">
                                        {{ $tech->category }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-center">
                                    @if ($tech->projects_count > 0)
                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-zinc-100 dark:bg-zinc-800 px-2.5 py-1 text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                                            <flux:icon.folder class="size-3 text-zinc-400" />
                                            {{ $tech->projects_count }} {{ __('projects') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-zinc-400">—</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $tech->created_at?->format('M d, Y') ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap py-4 ps-3 pe-4 text-right text-sm sm:pe-6">
                                    <flux:dropdown position="bottom" align="end">
                                        <flux:button variant="ghost" size="xs" icon="ellipsis-horizontal" />
                                        <flux:menu>
                                            <flux:menu.item icon="pencil-square" wire:click="openEditModal('{{ $tech->tech_id }}')">
                                                {{ __('Edit') }}
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item variant="danger" icon="trash" wire:click="confirmDelete('{{ $tech->tech_id }}')">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($this->technologies->hasPages())
                <div class="border-t border-zinc-200 dark:border-zinc-800 p-4">
                    {{ $this->technologies->links() }}
                </div>
            @endif
        </flux:card>
    @endif

    {{-- MODAL: CREATE TECHNOLOGY --}}
    <flux:modal wire:model="showCreateModal" class="md:w-md">
        <form wire:submit="createTechnology" class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('Add Technology') }}</flux:heading>
                <flux:subheading>{{ __('Register a new framework, language, or tool in the stack.') }}</flux:subheading>
            </div>

            <flux:input
                wire:model="techName"
                :label="__('Technology / Tool Name')"
                placeholder="e.g. Next.js, Redis, Tailwind CSS"
                required
                autofocus
            />

            <div class="space-y-1">
                <flux:select wire:model="category" :label="__('Category')">
                    @foreach ($this->defaultCategories() as $cat)
                        <flux:select.option :value="$cat">{{ $cat }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save Technology') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- MODAL: EDIT TECHNOLOGY --}}
    <flux:modal wire:model="showEditModal" class="md:w-md">
        <form wire:submit="updateTechnology" class="space-y-5">
            <div>
                <flux:heading size="lg">{{ __('Edit Technology') }}</flux:heading>
                <flux:subheading>{{ __('Update technology name or category classification.') }}</flux:subheading>
            </div>

            <flux:input
                wire:model="techName"
                :label="__('Technology / Tool Name')"
                placeholder="e.g. Next.js, Redis, Tailwind CSS"
                required
            />

            <div class="space-y-1">
                <flux:select wire:model="category" :label="__('Category')">
                    @foreach ($this->defaultCategories() as $cat)
                        <flux:select.option :value="$cat">{{ $cat }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Update Technology') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- MODAL: CONFIRM DELETE --}}
    <flux:modal wire:model="showDeleteModal" class="md:w-md">
        <div class="space-y-5">
            <div class="flex items-start gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-950/50 text-red-600 dark:text-red-400">
                    <flux:icon.exclamation-triangle class="size-5" />
                </div>
                <div>
                    <flux:heading size="lg">{{ __('Delete Technology') }}</flux:heading>
                    <flux:subheading class="mt-1">
                        {{ __('Are you sure you want to remove') }} <strong class="text-zinc-900 dark:text-zinc-100">{{ $deleteTechName }}</strong>?
                    </flux:subheading>
                </div>
            </div>

            @if ($deleteProjectsCount > 0)
                <div class="rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 p-3 text-xs text-amber-800 dark:text-amber-300">
                    <div class="flex items-center gap-1.5 font-semibold">
                        <flux:icon.exclamation-circle class="size-4" />
                        <span>{{ __('Warning: Associated Projects') }}</span>
                    </div>
                    <p class="mt-1">
                        {{ __('This technology is currently linked to') }} <strong>{{ $deleteProjectsCount }}</strong> {{ __('project(s). Removing it will detach it from those projects.') }}
                    </p>
                </div>
            @endif

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="deleteTechnology">{{ __('Delete Technology') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
