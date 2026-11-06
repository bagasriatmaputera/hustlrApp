<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Access Approvals') }}</flux:heading>
            <flux:subheading>{{ __('Review self-registered users and grant module access.') }}</flux:subheading>
        </div>
    </div>

    <flux:card class="!p-0 overflow-hidden shadow-xs">
        {{-- Mobile Cards View (Visible on < md) --}}
        <div class="block md:hidden divide-y divide-zinc-200 dark:divide-zinc-800">
            @forelse ($this->pendingUsers as $pendingUser)
                <div wire:key="mobile-approval-{{ $pendingUser->id }}" class="flex flex-col gap-3 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-accent-content text-accent-foreground font-semibold text-xs shadow-xs">
                                {{ $pendingUser->initials() }}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100 truncate text-sm">
                                    {{ $pendingUser->full_name ?? $pendingUser->name }}
                                </span>
                                <span class="text-xs text-zinc-500 truncate">{{ $pendingUser->email }}</span>
                            </div>
                        </div>
                        <span class="text-[11px] text-zinc-400 shrink-0">
                            {{ $pendingUser->created_at?->diffForHumans() }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Requested Modules') }}</span>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse ($pendingUser->requested_modules ?? [] as $module)
                                <flux:badge color="blue" size="sm">{{ $this->moduleLabels[$module] ?? $module }}</flux:badge>
                            @empty
                                <flux:badge size="sm">{{ __('None requested') }}</flux:badge>
                            @endforelse
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800/60">
                        <flux:button size="sm" variant="primary" icon="check" wire:click="approve({{ $pendingUser->id }})" wire:confirm="{{ __('Approve :name?', ['name' => $pendingUser->email]) }}" class="w-full justify-center">
                            {{ __('Approve') }}
                        </flux:button>
                        <flux:button size="sm" variant="danger" icon="x-mark" wire:click="reject({{ $pendingUser->id }})" wire:confirm="{{ __('Reject :name?', ['name' => $pendingUser->email]) }}" class="w-full justify-center">
                            {{ __('Reject') }}
                        </flux:button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-zinc-500">
                    {{ __('No pending requests.') }}
                </div>
            @endforelse
        </div>

        {{-- Desktop / Tablet Table View (Visible on >= md) --}}
        <div class="hidden md:block overflow-x-auto">
            <flux:table class="w-full min-w-[640px]">
                <flux:table.columns>
                    <flux:table.column class="!ps-6 !py-4 min-w-48">{{ __('User') }}</flux:table.column>
                    <flux:table.column class="!px-4 !py-4 min-w-44">{{ __('Requested Modules') }}</flux:table.column>
                    <flux:table.column class="!px-4 !py-4 min-w-32">{{ __('Registered') }}</flux:table.column>
                    <flux:table.column class="!pe-6 !py-4 w-44 min-w-44" align="end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->pendingUsers as $pendingUser)
                        <flux:table.row :key="$pendingUser->id">
                            <flux:table.cell class="!ps-6 !py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-accent-content text-accent-foreground font-semibold text-xs shadow-xs">
                                        {{ $pendingUser->initials() }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ $pendingUser->full_name ?? $pendingUser->name }}</span>
                                        <span class="text-xs text-zinc-500 truncate">{{ $pendingUser->email }}</span>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="!px-4 !py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($pendingUser->requested_modules ?? [] as $module)
                                        <flux:badge color="blue" size="sm">{{ $this->moduleLabels[$module] ?? $module }}</flux:badge>
                                    @empty
                                        <flux:badge size="sm">{{ __('None requested') }}</flux:badge>
                                    @endforelse
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="!px-4 !py-4 text-sm text-zinc-500">
                                {{ $pendingUser->created_at?->diffForHumans() }}
                            </flux:table.cell>
                            <flux:table.cell class="!pe-6 !py-4" align="end">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" variant="primary" icon="check" wire:click="approve({{ $pendingUser->id }})" wire:confirm="{{ __('Approve :name?', ['name' => $pendingUser->email]) }}">
                                        {{ __('Approve') }}
                                    </flux:button>
                                    <flux:button size="sm" variant="danger" icon="x-mark" wire:click="reject({{ $pendingUser->id }})" wire:confirm="{{ __('Reject :name?', ['name' => $pendingUser->email]) }}">
                                        {{ __('Reject') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="4" class="!py-10 text-center text-zinc-500">
                                {{ __('No pending requests.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>

    <div>
        {{ $this->pendingUsers->links() }}
    </div>
</div>
