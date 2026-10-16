<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('Access Approvals') }}</flux:heading>
            <flux:subheading>{{ __('Review self-registered users and grant module access.') }}</flux:subheading>
        </div>
    </div>

    <flux:card class="!p-0 overflow-hidden shadow-xs">
        <flux:table class="w-full">
            <flux:table.columns>
                <flux:table.column class="!ps-6 !py-4">{{ __('User') }}</flux:table.column>
                <flux:table.column class="!px-4 !py-4">{{ __('Requested Modules') }}</flux:table.column>
                <flux:table.column class="!px-4 !py-4">{{ __('Registered') }}</flux:table.column>
                <flux:table.column class="!pe-6 !py-4" align="end">{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->pendingUsers as $pendingUser)
                    <flux:table.row :key="$pendingUser->id">
                        <flux:table.cell class="!ps-6 !py-4">
                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $pendingUser->full_name ?? $pendingUser->name }}</span>
                                <span class="text-xs text-zinc-500">{{ $pendingUser->email }}</span>
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
    </flux:card>

    <div>
        {{ $this->pendingUsers->links() }}
    </div>
</div>
