<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Work Experience') }}</flux:heading>
            <flux:subheading>{{ __('Manage your professional work experience.') }}</flux:subheading>
        </div>
        @can('PORTFOLIO - Can Manage Experiences')
            <flux:button variant="primary" icon="plus" :href="route('portfolio.experiences.create')" wire:navigate>
                {{ __('Add Experience') }}
            </flux:button>
        @endcan
    </div>

    @if ($this->experiences->isEmpty())
        <flux:card class="!py-16">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="mb-4 flex size-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.briefcase class="size-8 text-zinc-400" />
                </div>
                <flux:heading size="lg" class="mb-1">{{ __('No experience entries yet') }}</flux:heading>
                <flux:subheading class="mb-6 max-w-sm">{{ __('Add your work experience to showcase your career history.') }}</flux:subheading>
                @can('PORTFOLIO - Can Manage Experiences')
                    <flux:button variant="primary" icon="plus" :href="route('portfolio.experiences.create')" wire:navigate>
                        {{ __('Add First Experience') }}
                    </flux:button>
                @endcan
            </div>
        </flux:card>
    @else
        <div class="flex flex-col gap-4">
            @foreach ($this->experiences as $experience)
                <flux:card wire:key="experience-{{ $experience->experience_id }}" class="group flex flex-col gap-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex min-w-0 flex-1 flex-col gap-1">
                            <div class="flex flex-wrap gap-1.5">
                                <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                    {{ $experience->workplace_type }}
                                </span>
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $experience->contract_type }}
                                </span>
                                @if (!$experience->end_date)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                        {{ __('Current') }}
                                    </span>
                                @endif
                            </div>
                            <flux:heading size="lg">{{ $experience->position }}</flux:heading>
                            <flux:text class="font-medium">{{ $experience->company_name }}</flux:text>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-zinc-500">
                                <span class="flex items-center gap-1">
                                    <flux:icon.calendar class="size-3.5" />
                                    {{ $experience->start_date->format('M Y') }} — {{ $experience->end_date?->format('M Y') ?? __('Present') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <flux:icon.map-pin class="size-3.5" />
                                    {{ $experience->location }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button variant="ghost" size="sm" icon="pencil-square" :href="route('portfolio.experiences.edit', $experience->experience_id)" wire:navigate />
                            <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete('{{ $experience->experience_id }}')" wire:confirm="{{ __('Delete this experience and all its job descriptions?') }}" />
                        </div>
                    </div>

                    @if ($experience->jobDescs->isNotEmpty())
                        <div class="flex flex-col gap-1.5 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                            <flux:text class="text-xs font-semibold uppercase tracking-wider text-zinc-500">{{ __('Highlights') }}</flux:text>
                            <ul class="flex flex-col gap-1">
                                @foreach ($experience->jobDescs as $jobDesc)
                                    <li class="flex items-start gap-2">
                                        <span class="mt-1.5 size-1.5 flex-shrink-0 rounded-full bg-zinc-400"></span>
                                        <flux:text class="text-sm">{{ $jobDesc->description }}</flux:text>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </flux:card>
            @endforeach
        </div>
    @endif
</div>
