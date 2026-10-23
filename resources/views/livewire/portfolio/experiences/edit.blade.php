<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-start gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('portfolio.experiences.index')" wire:navigate class="shrink-0" />
        <div class="min-w-0">
            <flux:heading size="xl" class="break-words">{{ __('Edit Work Experience') }}</flux:heading>
            <flux:subheading class="break-words">{{ __('Update an existing entry in your work history.') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="flex w-full flex-col gap-4 sm:gap-6">
        <flux:card class="flex flex-col gap-6">
            <flux:heading size="lg">{{ __('Company & Role') }}</flux:heading>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="company_name" :label="__('Company Name')" :placeholder="__('e.g. The One Retail')" required />
                <flux:input wire:model="position" :label="__('Position / Role')" :placeholder="__('e.g. Software Engineer')" required />
            </div>
            <flux:input wire:model="location" :label="__('Location')" :placeholder="__('e.g. South Jakarta, Indonesia')" required />
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:select wire:model="workplace_type" :label="__('Workplace Type')" required>
                    <flux:select.option value="On-site">{{ __('On-site') }}</flux:select.option>
                    <flux:select.option value="Remote">{{ __('Remote') }}</flux:select.option>
                    <flux:select.option value="Hybrid">{{ __('Hybrid') }}</flux:select.option>
                </flux:select>
                <flux:select wire:model="contract_type" :label="__('Contract Type')" required>
                    <flux:select.option value="Full-time">{{ __('Full-time') }}</flux:select.option>
                    <flux:select.option value="Part-time">{{ __('Part-time') }}</flux:select.option>
                    <flux:select.option value="Internship">{{ __('Internship') }}</flux:select.option>
                    <flux:select.option value="Contract">{{ __('Contract') }}</flux:select.option>
                    <flux:select.option value="Training">{{ __('Training') }}</flux:select.option>
                    <flux:select.option value="Freelance">{{ __('Freelance') }}</flux:select.option>
                </flux:select>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-6">
            <flux:heading size="lg">{{ __('Period') }}</flux:heading>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="start_date" :label="__('Start Date')" type="date" required />
                <flux:input wire:model="end_date" :label="__('End Date')" type="date" />
            </div>
            <flux:input wire:model="order" :label="__('Display Order')" type="number" min="0" />
        </flux:card>

        <flux:card class="flex flex-col gap-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <flux:heading size="lg">{{ __('Job Descriptions / Highlights') }}</flux:heading>
                <flux:button variant="ghost" size="sm" icon="plus" wire:click="addJobDesc" class="w-full sm:w-auto">
                    {{ __('Add Item') }}
                </flux:button>
            </div>

            <div class="flex flex-col gap-3">
                @foreach ($job_descs as $i => $jobDesc)
                    <div wire:key="job-desc-{{ $i }}" class="flex flex-col gap-2 sm:flex-row sm:items-start">
                        <div class="w-full min-w-0 flex-1">
                            <flux:textarea
                                wire:model="job_descs.{{ $i }}.description"
                                :placeholder="__('Describe a key responsibility or achievement...')"
                                rows="2"
                                required
                            />
                        </div>
                        @if (count($job_descs) > 1)
                            <flux:button variant="ghost" size="sm" icon="trash" class="self-end text-red-500 hover:text-red-600 sm:mt-1 sm:self-start" wire:click="removeJobDesc({{ $i }})" />
                        @endif
                    </div>
                @endforeach
            </div>
        </flux:card>

        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <flux:button variant="ghost" :href="route('portfolio.experiences.index')" wire:navigate class="w-full sm:w-auto">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto">
                {{ __('Update Experience') }}
            </flux:button>
        </div>
    </form>
</div>
