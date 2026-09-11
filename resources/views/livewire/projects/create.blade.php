{{-- <x-layouts::app :title="__('Add Project')"> --}}
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center gap-3">
            <flux:button variant="ghost" icon="arrow-left" :href="route('projects.index')" wire:navigate />
            <div>
                <flux:heading size="xl">{{ __('Add Project') }}</flux:heading>
                <flux:subheading>{{ __('Create a new project to track and manage.') }}</flux:subheading>
            </div>
        </div>

        <form wire:submit="save" class="flex w-full flex-col gap-6">
            <flux:card class="flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Project Information') }}</flux:heading>

                <flux:input wire:model="project_name" :label="__('Project Name')" :placeholder="__('Enter project name')" required />

                <flux:textarea wire:model="description" :label="__('Description')" :placeholder="__('Describe the project...')" rows="4" />

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:select wire:model="ownership_type" :label="__('Ownership Type')" :placeholder="__('Select type')" required>
                        <flux:select.option value="">{{ __('Select type') }}</flux:select.option>
                        <flux:select.option value="Internal">{{ __('Internal') }}</flux:select.option>
                        <flux:select.option value="Client">{{ __('Client') }}</flux:select.option>
                        <flux:select.option value="Freelance">{{ __('Freelance') }}</flux:select.option>
                    </flux:select>

                    <flux:select wire:model="status_id" :label="__('Status')" :placeholder="__('Select status')" required>
                        <flux:select.option value="">{{ __('Select status') }}</flux:select.option>
                        @foreach ($this->statuses as $status)
                            <flux:select.option :value="$status->status_id">{{ $status->status_name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:select wire:model="client_id" :label="__('Client (Optional)')" :placeholder="__('Select client')">
                    <flux:select.option value="">{{ __('No client') }}</flux:select.option>
                    @foreach ($this->clients as $client)
                        <flux:select.option :value="$client->client_id">{{ $client->client_name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </flux:card>

            <flux:card class="flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Timeline') }}</flux:heading>

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input wire:model="start_date" :label="__('Start Date')" type="date" />
                    <flux:input wire:model="end_date" :label="__('End Date')" type="date" />
                </div>
            </flux:card>

            <flux:card class="flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Links') }}</flux:heading>

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input wire:model="link_testing" :label="__('Testing URL')" :placeholder="__('https://staging.example.com')" type="url" />
                    <flux:input wire:model="link_prod" :label="__('Production URL')" :placeholder="__('https://example.com')" type="url" />
                </div>
            </flux:card>

            <flux:card class="flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Technologies') }}</flux:heading>
                <flux:subheading>{{ __('Select the technologies used in this project.') }}</flux:subheading>

                @if ($this->technologies->isNotEmpty())
                    @php
                        $groupedTechnologies = $this->technologies->groupBy('category');
                    @endphp
                    <div class="flex flex-col gap-4">
                        @foreach ($groupedTechnologies as $category => $techs)
                            <div>
                                <flux:text class="mb-2 text-xs font-semibold uppercase tracking-wider">{{ $category }}</flux:text>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($techs as $tech)
                                        <label wire:key="tech-{{ $tech->tech_id }}" class="cursor-pointer">
                                            <input type="checkbox" wire:model="selectedTechnologies" value="{{ $tech->tech_id }}" class="peer hidden" />
                                            <span class="inline-flex items-center rounded-lg border border-zinc-200 px-3 py-1.5 text-sm font-medium text-zinc-600 transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 dark:border-zinc-700 dark:text-zinc-400 dark:peer-checked:border-blue-500 dark:peer-checked:bg-blue-900/30 dark:peer-checked:text-blue-300">
                                                {{ $tech->tech_name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <flux:text class="text-sm text-zinc-500">{{ __('No technologies available. Add technologies first.') }}</flux:text>
                @endif
            </flux:card>

            <div class="flex items-center justify-end gap-3">
                <flux:button variant="ghost" :href="route('projects.index')" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                    {{ __('Create Project') }}
                </flux:button>
            </div>
        </form>
    </div>
{{-- </x-layouts::app>  --}}

