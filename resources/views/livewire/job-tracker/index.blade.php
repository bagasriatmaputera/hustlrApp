<div class="flex h-full w-full flex-1 flex-col gap-6">
    {{-- Header & Add Action --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Job Application Tracker') }}</flux:heading>
            <flux:subheading>{{ __('Monitor your job applications, recruitment stages, interviews, and offerings.') }}</flux:subheading>
        </div>

        <div class="flex items-center gap-2">
            <flux:dropdown position="bottom" align="end">
                <flux:button variant="ghost" size="sm" icon="arrow-down-tray" icon:trailing="chevron-down">
                    {{ __('Export') }}
                </flux:button>

                <flux:menu>
                    <flux:menu.item
                        :href="route('job-tracker.export.csv', array_filter(['search' => $search, 'contract_status' => $contractFilter, 'apply_status' => $statusFilter, 'workplace_type' => $workplaceFilter]))"
                        icon="document-text"
                    >
                        {{ __('Export CSV') }}
                    </flux:menu.item>
                    <flux:menu.item
                        :href="route('job-tracker.export.pdf', array_filter(['search' => $search, 'contract_status' => $contractFilter, 'apply_status' => $statusFilter, 'workplace_type' => $workplaceFilter]))"
                        icon="document"
                    >
                        {{ __('Export PDF') }}
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            <flux:button variant="primary" icon="plus" wire:click="openCreateModal">
                {{ __('Add Job Application') }}
            </flux:button>
        </div>
    </div>

    {{-- Quick Metric Overview Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
        {{-- Total --}}
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                    <flux:icon.briefcase class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-500 font-medium">{{ __('Total Applied') }}</flux:text>
                    <flux:heading size="xl">{{ $this->totalApplicationsCount }}</flux:heading>
                </div>
            </div>
        </flux:card>

        {{-- HR Review --}}
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400">
                    <flux:icon.clock class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-500 font-medium">{{ __('HR Review') }}</flux:text>
                    <flux:heading size="xl">{{ $this->hrReviewCount }}</flux:heading>
                </div>
            </div>
        </flux:card>

        {{-- Interview Stages --}}
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400">
                    <flux:icon.chat-bubble-left-right class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-500 font-medium">{{ __('In Interview') }}</flux:text>
                    <flux:heading size="xl">{{ $this->interviewCount }}</flux:heading>
                </div>
            </div>
        </flux:card>

        {{-- Offering / Accepted --}}
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                    <flux:icon.document-check class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-500 font-medium">{{ __('Offered / Accepted') }}</flux:text>
                    <flux:heading size="xl">{{ $this->offeringCount }}</flux:heading>
                </div>
            </div>
        </flux:card>

        {{-- Rejected --}}
        <flux:card class="!p-4 col-span-2 lg:col-span-1">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400">
                    <flux:icon.x-circle class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-500 font-medium">{{ __('Rejected') }}</flux:text>
                    <flux:heading size="xl">{{ $this->rejectedCount }}</flux:heading>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Filter & Search Toolbar --}}
    <div class="flex flex-col lg:flex-row gap-3 items-stretch lg:items-center justify-between">
        <div class="flex flex-col sm:flex-row flex-1 gap-3">
            <div class="w-full sm:w-72">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search company, role, location...') }}"
                    icon="magnifying-glass"
                    clearable
                />
            </div>

            <div class="w-full sm:w-48">
                <flux:select wire:model.live="contractFilter" placeholder="{{ __('Contract Type') }}">
                    <flux:select.option value="">{{ __('All Contract Types') }}</flux:select.option>
                    @foreach (\App\Models\JobApplication::contractStatuses() as $key => $label)
                        <flux:select.option :value="$key">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="w-full sm:w-48">
                <flux:select wire:model.live="statusFilter" placeholder="{{ __('Apply Status') }}">
                    <flux:select.option value="">{{ __('All Apply Statuses') }}</flux:select.option>
                    @foreach (\App\Models\JobApplication::applyStatuses() as $key => $label)
                        <flux:select.option :value="$key">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="w-full sm:w-40">
                <flux:select wire:model.live="workplaceFilter" placeholder="{{ __('Workplace') }}">
                    <flux:select.option value="">{{ __('All Workplaces') }}</flux:select.option>
                    @foreach (\App\Models\JobApplication::workplaceTypes() as $key => $label)
                        <flux:select.option :value="$key">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        @if ($search || $contractFilter || $statusFilter || $workplaceFilter)
            <div class="flex items-center gap-2">
                <flux:button variant="ghost" size="sm" wire:click="clearFilters" icon="x-mark">
                    {{ __('Clear filters') }}
                </flux:button>
            </div>
        @endif
    </div>

    {{-- Job Applications Table --}}
    <flux:card class="!p-0 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <flux:table class="w-full">
                <flux:table.columns>
                    <flux:table.column class="!ps-6 !py-4 w-12 text-center">{{ __('No') }}</flux:table.column>
                    <flux:table.column class="!px-4 !py-4 min-w-56">{{ __('Company & Position') }}</flux:table.column>
                    <flux:table.column class="!px-4 !py-4 min-w-36">{{ __('Contract Status') }}</flux:table.column>
                    <flux:table.column class="!px-4 !py-4 min-w-44">{{ __('Status Apply') }}</flux:table.column>
                    <flux:table.column class="!px-4 !py-4 min-w-64">{{ __('Detail (Gaji, Lokasi, Benefits)') }}</flux:table.column>
                    <flux:table.column class="!pe-6 !py-4 w-28" align="end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->jobApplications as $index => $job)
                        @php
                            $applyConfig = \App\Models\JobApplication::getApplyStatusConfig($job->apply_status);
                            $contractConfig = \App\Models\JobApplication::getContractStatusConfig($job->contract_status);
                        @endphp
                        <flux:table.row :key="$job->job_application_id" class="hover:bg-zinc-50/75 dark:hover:bg-zinc-800/60 transition-colors">
                            {{-- Number Column --}}
                            <flux:table.cell class="!ps-6 !py-4 text-center font-mono text-xs text-zinc-400">
                                {{ $index + 1 }}
                            </flux:table.cell>

                            {{-- Company & Position --}}
                            <flux:table.cell class="!px-4 !py-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 font-bold text-sm shadow-2xs border border-zinc-200/60 dark:border-zinc-700/60">
                                        {{ mb_substr($job->company_name, 0, 2) }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">
                                                {{ $job->company_name }}
                                            </span>
                                            @if ($job->job_url)
                                                <a href="{{ $job->job_url }}" target="_blank" rel="noopener noreferrer" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors" title="{{ __('View Job Posting') }}">
                                                    <flux:icon.arrow-top-right-on-square class="size-3.5" />
                                                </a>
                                            @endif
                                        </div>
                                        <button type="button" wire:click="openShowModal('{{ $job->job_application_id }}')" class="text-start font-medium text-xs text-primary hover:underline truncate mt-0.5">
                                            {{ $job->position }}
                                        </button>
                                        @if ($job->applied_at)
                                            <span class="text-[11px] text-zinc-400 mt-0.5">
                                                {{ __('Applied:') }} {{ $job->applied_at->format('d M Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>

                            {{-- Contract Status --}}
                            <flux:table.cell class="!px-4 !py-4">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-medium {{ $contractConfig['bg'] }} {{ $contractConfig['text'] }}">
                                    {{ $contractConfig['label'] }}
                                </span>
                            </flux:table.cell>

                            {{-- Apply Status with Dropdown Change Option --}}
                            <flux:table.cell class="!px-4 !py-4">
                                <flux:dropdown position="bottom" align="start">
                                    <button type="button" class="group inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $applyConfig['bg'] }} {{ $applyConfig['text'] }} hover:opacity-90 transition-opacity">
                                        <span class="size-1.5 rounded-full {{ $applyConfig['dot'] }}"></span>
                                        <span>{{ $applyConfig['label'] }}</span>
                                        <flux:icon.chevron-down class="size-3 opacity-60 group-hover:opacity-100 transition-opacity" />
                                    </button>

                                    <flux:menu>
                                        <flux:menu.heading>{{ __('Change Status') }}</flux:menu.heading>
                                        @foreach (\App\Models\JobApplication::applyStatuses() as $statusKey => $statusName)
                                            <flux:menu.item wire:click="updateStatus('{{ $job->job_application_id }}', '{{ $statusKey }}')">
                                                @if ($job->apply_status === $statusKey)
                                                    <flux:icon.check class="size-4 text-emerald-500 mr-1" />
                                                @endif
                                                {{ $statusName }}
                                            </flux:menu.item>
                                        @endforeach
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>

                            {{-- Detail (Salary, Location, Benefits) --}}
                            <flux:table.cell class="!px-4 !py-4">
                                <div class="flex flex-col gap-1 text-xs">
                                    {{-- Salary --}}
                                    @if ($job->salary)
                                        <div class="flex items-center gap-1.5 font-medium text-emerald-700 dark:text-emerald-400">
                                            <flux:icon.banknotes class="size-3.5 shrink-0" />
                                            <span>{{ $job->salary }}</span>
                                        </div>
                                    @endif

                                    {{-- Location & Workplace --}}
                                    <div class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400">
                                        <flux:icon.map-pin class="size-3.5 shrink-0 text-zinc-400" />
                                        <span class="truncate">{{ $job->location ?? __('Location not set') }}</span>
                                        <span class="rounded bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.2 text-[10px] uppercase font-semibold text-zinc-500">
                                            {{ $job->workplace_type }}
                                        </span>
                                    </div>

                                    {{-- Benefits Preview --}}
                                    @if ($job->benefits)
                                        <div class="flex items-center gap-1 text-zinc-500 dark:text-zinc-400 line-clamp-1 text-[11px]" title="{{ $job->benefits }}">
                                            <flux:icon.gift class="size-3.5 shrink-0 text-amber-500" />
                                            <span class="truncate">{{ Str::limit(str_replace("\n", ', ', $job->benefits), 50) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            {{-- Actions --}}
                            <flux:table.cell class="!pe-6 !py-4" align="end">
                                <div class="flex items-center justify-end gap-1">
                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="eye"
                                        wire:click="openShowModal('{{ $job->job_application_id }}')"
                                        title="{{ __('View Details') }}"
                                    />

                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="pencil-square"
                                        wire:click="openEditModal('{{ $job->job_application_id }}')"
                                        title="{{ __('Edit Application') }}"
                                    />

                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="trash"
                                        class="text-red-600 hover:text-red-700 dark:text-red-400"
                                        wire:click="confirmDelete('{{ $job->job_application_id }}')"
                                        title="{{ __('Delete Application') }}"
                                    />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center !py-16 text-zinc-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="mb-3 flex size-14 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400">
                                        <flux:icon.briefcase class="size-7" />
                                    </div>
                                    <p class="font-semibold text-base text-zinc-800 dark:text-zinc-200">{{ __('No job applications found') }}</p>
                                    <p class="text-xs text-zinc-400 mt-1 max-w-sm">
                                        @if ($search || $contractFilter || $statusFilter || $workplaceFilter)
                                            {{ __('No matches found for your filter criteria. Try clearing or relaxing your search.') }}
                                        @else
                                            {{ __('Start tracking your job applications by adding your first position.') }}
                                        @endif
                                    </p>
                                    <div class="mt-4">
                                        @if ($search || $contractFilter || $statusFilter || $workplaceFilter)
                                            <flux:button variant="primary" size="sm" wire:click="clearFilters" icon="arrow-path">
                                                {{ __('Reset Filters') }}
                                            </flux:button>
                                        @else
                                            <flux:button variant="primary" size="sm" icon="plus" wire:click="openCreateModal">
                                                {{ __('Add First Job Application') }}
                                            </flux:button>
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        {{-- Infinite Scroll Observer & Load More Indicator --}}
        <div class="border-t border-zinc-200 dark:border-zinc-800 p-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-zinc-500">
                <div>
                    {{ __('Showing') }} <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $this->jobApplications->count() }}</span>
                    {{ __('of') }} <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $this->jobApplications->total() }}</span> {{ __('jobs') }}
                </div>

                @if ($this->jobApplications->hasMorePages())
                    <div
                        wire:intersect="loadMore"
                        class="flex items-center gap-2 text-primary font-medium cursor-pointer py-1 px-3 rounded-lg bg-primary/10 hover:bg-primary/20 transition-colors"
                        wire:click="loadMore"
                    >
                        <flux:icon.arrow-path class="size-4 animate-spin" wire:loading wire:target="loadMore" />
                        <flux:icon.arrow-down class="size-4" wire:loading.remove wire:target="loadMore" />
                        <span>{{ __('Scroll down or click to load more...') }}</span>
                    </div>
                @else
                    @if ($this->jobApplications->total() > 0)
                        <div class="flex items-center gap-1.5 text-zinc-400">
                            <flux:icon.check-circle class="size-4 text-emerald-500" />
                            <span>{{ __('All applications loaded') }}</span>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </flux:card>

    {{-- MODAL 1: CREATE JOB APPLICATION --}}
    <flux:modal wire:model="showCreateModal" class="md:w-2xl">
        <form wire:submit="createJob" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('Add New Job Application') }}</flux:heading>
                <flux:subheading>{{ __('Track a new job opening, interview stages, and compensation details.') }}</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    wire:model="company_name"
                    :label="__('Company Name')"
                    placeholder="e.g. Tokopedia, Gojek, TechCorp"
                    required
                />

                <flux:input
                    wire:model="position"
                    :label="__('Job Position / Title')"
                    placeholder="e.g. Senior Laravel Engineer"
                    required
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <flux:select wire:model="contract_status" :label="__('Contract Status')" required>
                    @foreach (\App\Models\JobApplication::contractStatuses() as $key => $label)
                        <flux:select.option :value="$key">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="apply_status" :label="__('Apply Status')" required>
                    @foreach (\App\Models\JobApplication::applyStatuses() as $key => $label)
                        <flux:select.option :value="$key">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="workplace_type" :label="__('Workplace Type')" required>
                    @foreach (\App\Models\JobApplication::workplaceTypes() as $key => $label)
                        <flux:select.option :value="$key">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    wire:model="salary"
                    :label="__('Salary / Compensation')"
                    placeholder="e.g. Rp 20.000.000 - Rp 30.000.000 or $3,500/mo"
                />

                <flux:input
                    wire:model="location"
                    :label="__('Location')"
                    placeholder="e.g. Jakarta Selatan / Singapore"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    wire:model="job_url"
                    type="url"
                    :label="__('Job Posting Link / URL')"
                    placeholder="https://company.com/careers/..."
                />

                <flux:input
                    wire:model="applied_at"
                    type="date"
                    :label="__('Date Applied')"
                />
            </div>

            <flux:textarea
                wire:model="benefits"
                :label="__('Benefits & Perks (Gaji, BPJS, Laptop, Bonus, dll)')"
                placeholder="e.g. BPJS Kesehatan & Ketenagakerjaan&#10;MacBook Pro M3 Max&#10;100% Work from Anywhere&#10;Annual Performance Bonus"
                rows="3"
            />

            <flux:textarea
                wire:model="notes"
                :label="__('Notes & Recruitment Progress')"
                placeholder="e.g. Interview round scheduled with engineering manager next Tuesday. Review microservices and Redis caching."
                rows="3"
            />

            <div class="flex justify-end gap-2 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save Application') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- MODAL 2: SHOW / DETAIL JOB APPLICATION --}}
    <flux:modal wire:model="showDetailModal" class="md:w-2xl">
        @if ($viewingJob)
            @php
                $vApplyConfig = \App\Models\JobApplication::getApplyStatusConfig($viewingJob->apply_status);
                $vContractConfig = \App\Models\JobApplication::getContractStatusConfig($viewingJob->contract_status);
            @endphp
            <div class="space-y-5">
                {{-- Header with Company & Position --}}
                <div class="flex items-start justify-between gap-3 border-b border-zinc-200 dark:border-zinc-800 pb-4">
                    <div class="flex items-start gap-3">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary font-bold text-lg border border-primary/20">
                            {{ mb_substr($viewingJob->company_name, 0, 2) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <flux:heading size="xl">{{ $viewingJob->position }}</flux:heading>
                            </div>
                            <flux:subheading class="font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $viewingJob->company_name }}
                            </flux:subheading>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-1.5">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $vApplyConfig['bg'] }} {{ $vApplyConfig['text'] }}">
                            <span class="size-1.5 rounded-full {{ $vApplyConfig['dot'] }}"></span>
                            {{ $vApplyConfig['label'] }}
                        </span>
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium {{ $vContractConfig['bg'] }} {{ $vContractConfig['text'] }}">
                            {{ $vContractConfig['label'] }}
                        </span>
                    </div>
                </div>

                {{-- Key Information Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/70 dark:border-zinc-700/70">
                        <span class="text-[11px] text-zinc-400 font-medium block mb-1">{{ __('Compensation / Salary') }}</span>
                        <p class="font-semibold text-sm text-emerald-600 dark:text-emerald-400">
                            {{ $viewingJob->salary ?: __('Not specified') }}
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/70 dark:border-zinc-700/70">
                        <span class="text-[11px] text-zinc-400 font-medium block mb-1">{{ __('Workplace & Location') }}</span>
                        <p class="font-medium text-sm text-zinc-800 dark:text-zinc-200 truncate">
                            {{ $viewingJob->location ?: '-' }}
                        </p>
                        <span class="text-[11px] font-bold uppercase text-zinc-500">{{ $viewingJob->workplace_type }}</span>
                    </div>

                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/70 dark:border-zinc-700/70 col-span-2 sm:col-span-1">
                        <span class="text-[11px] text-zinc-400 font-medium block mb-1">{{ __('Applied Date') }}</span>
                        <p class="font-medium text-sm text-zinc-800 dark:text-zinc-200">
                            {{ $viewingJob->applied_at ? $viewingJob->applied_at->format('d M Y') : '-' }}
                        </p>
                    </div>
                </div>

                {{-- Job Link --}}
                @if ($viewingJob->job_url)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-primary/5 border border-primary/20">
                        <div class="flex items-center gap-2 min-w-0">
                            <flux:icon.link class="size-4 text-primary shrink-0" />
                            <span class="text-xs text-zinc-600 dark:text-zinc-300 truncate">{{ $viewingJob->job_url }}</span>
                        </div>
                        <a href="{{ $viewingJob->job_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline shrink-0 ml-2">
                            <span>{{ __('Open Link') }}</span>
                            <flux:icon.arrow-top-right-on-square class="size-3.5" />
                        </a>
                    </div>
                @endif

                {{-- Benefits Section --}}
                @if ($viewingJob->benefits)
                    <div>
                        <flux:heading size="sm" class="mb-2 flex items-center gap-1.5">
                            <flux:icon.gift class="size-4 text-amber-500" />
                            <span>{{ __('Benefits & Compensation Package') }}</span>
                        </flux:heading>
                        <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/70 dark:border-zinc-700/70 text-xs leading-relaxed text-zinc-700 dark:text-zinc-300 whitespace-pre-line">
                            {{ $viewingJob->benefits }}
                        </div>
                    </div>
                @endif

                {{-- Notes Section --}}
                @if ($viewingJob->notes)
                    <div>
                        <flux:heading size="sm" class="mb-2 flex items-center gap-1.5">
                            <flux:icon.document-text class="size-4 text-blue-500" />
                            <span>{{ __('Progress Notes & Interview Log') }}</span>
                        </flux:heading>
                        <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/70 dark:border-zinc-700/70 text-xs leading-relaxed text-zinc-700 dark:text-zinc-300 whitespace-pre-line">
                            {{ $viewingJob->notes }}
                        </div>
                    </div>
                @endif

                {{-- Footer Actions --}}
                <div class="flex items-center justify-between pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:button
                        variant="ghost"
                        class="text-red-600 hover:text-red-700 dark:text-red-400"
                        icon="trash"
                        wire:click="confirmDelete('{{ $viewingJob->job_application_id }}')"
                    >
                        {{ __('Delete') }}
                    </flux:button>

                    <div class="flex items-center gap-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">{{ __('Close') }}</flux:button>
                        </flux:modal.close>
                        <flux:button variant="primary" icon="pencil-square" wire:click="openEditModal('{{ $viewingJob->job_application_id }}')">
                            {{ __('Edit Application') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        @endif
    </flux:modal>

    {{-- MODAL 3: EDIT JOB APPLICATION --}}
    <flux:modal wire:model="showEditModal" class="md:w-2xl">
        <form wire:submit="updateJob" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('Edit Job Application') }}</flux:heading>
                <flux:subheading>{{ __('Update position details, recruitment status, or salary info.') }}</flux:subheading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    wire:model="company_name"
                    :label="__('Company Name')"
                    required
                />

                <flux:input
                    wire:model="position"
                    :label="__('Job Position / Title')"
                    required
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <flux:select wire:model="contract_status" :label="__('Contract Status')" required>
                    @foreach (\App\Models\JobApplication::contractStatuses() as $key => $label)
                        <flux:select.option :value="$key">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="apply_status" :label="__('Apply Status')" required>
                    @foreach (\App\Models\JobApplication::applyStatuses() as $key => $label)
                        <flux:select.option :value="$key">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="workplace_type" :label="__('Workplace Type')" required>
                    @foreach (\App\Models\JobApplication::workplaceTypes() as $key => $label)
                        <flux:select.option :value="$key">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    wire:model="salary"
                    :label="__('Salary / Compensation')"
                    placeholder="e.g. Rp 20.000.000 - Rp 30.000.000"
                />

                <flux:input
                    wire:model="location"
                    :label="__('Location')"
                    placeholder="e.g. Jakarta Selatan"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    wire:model="job_url"
                    type="url"
                    :label="__('Job Posting Link / URL')"
                    placeholder="https://company.com/careers/..."
                />

                <flux:input
                    wire:model="applied_at"
                    type="date"
                    :label="__('Date Applied')"
                />
            </div>

            <flux:textarea
                wire:model="benefits"
                :label="__('Benefits & Perks')"
                rows="3"
            />

            <flux:textarea
                wire:model="notes"
                :label="__('Notes & Progress')"
                rows="3"
            />

            <div class="flex justify-end gap-2 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Update Changes') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- MODAL 4: CONFIRM DELETE --}}
    <flux:modal wire:model="showDeleteModal" class="md:w-md">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg" class="text-rose-600 dark:text-rose-400">
                    {{ __('Confirm Delete Job Application') }}
                </flux:heading>
                <flux:subheading>
                    {{ __('Are you sure you want to delete the job application for :title? This item can be soft-recovered if needed.', ['title' => $deletingJobTitle]) }}
                </flux:subheading>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="deleteJob">
                    {{ __('Delete Application') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
