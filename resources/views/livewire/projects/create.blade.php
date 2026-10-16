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

                <x-project-technologies :technologies="$this->technologies" :selected="$selectedTechnologies" />
            </flux:card>

            <flux:card class="flex flex-col gap-6">
                <flux:heading size="lg">{{ __('Team Members') }}</flux:heading>
                <flux:subheading>{{ __('Optionally add collaborators during project creation. You (creator) will be the project Owner.') }}</flux:subheading>

                @if ($this->availableUsers->isNotEmpty())
                    <flux:input wire:model.live.debounce.300ms="memberSearch" :placeholder="__('Search users...')" icon="magnifying-glass" clearable />
                    <div class="max-h-56 overflow-auto rounded-lg border border-zinc-200 p-2 dark:border-zinc-700">
                        @foreach ($this->availableUsers->take($memberPerPage) as $availUser)
                            <label class="flex items-center gap-2 rounded px-2 py-1 hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                <input type="checkbox" wire:model="selectedMembers" value="{{ $availUser->id }}" class="size-4 rounded border-zinc-300 text-primary focus:ring-primary" />
                                <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $availUser->full_name ?? $availUser->name }}</span>
                                <span class="ml-auto text-xs text-zinc-400">{{ $availUser->email }}</span>
                            </label>
                        @endforeach
                    </div>
                    @if ($this->availableUsers->count() > $memberPerPage)
                        <flux:button type="button" variant="ghost" size="sm" wire:click="loadMoreMembers">
                            {{ __('Load more (:n)', ['n' => $this->availableUsers->count() - $memberPerPage]) }}
                        </flux:button>
                    @endif
                    <flux:select wire:model="defaultMemberRole" :label="__('Default role for added members')">
                        <flux:select.option value="Member">{{ __('Member') }}</flux:select.option>
                        <flux:select.option value="Manager">{{ __('Manager') }}</flux:select.option>
                        <flux:select.option value="Owner">{{ __('Owner') }}</flux:select.option>
                    </flux:select>
                @else
                    <flux:text class="text-sm text-zinc-500">{{ __('No other users available. Invite users via UAM first.') }}</flux:text>
                @endif
            </flux:card>
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

