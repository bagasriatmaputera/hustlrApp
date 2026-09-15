<div class="flex h-full w-full flex-1 flex-col gap-6">
    {{-- Header & Actions --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl" level="1">{{ __('User Access Management (UAM)') }}</flux:heading>
            <flux:subheading>{{ __('Manage system users, access roles, and CRUD administrative permissions.') }}</flux:subheading>
        </div>

        @can('IPS - UAM - Can Create Users')
            <flux:button variant="primary" icon="user-plus" wire:click="openCreateModal">
                {{ __('Add New User') }}
            </flux:button>
        @endcan
    </div>

    {{-- Quick Metric Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                    <flux:icon.users class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-500 font-medium">{{ __('Total Users') }}</flux:text>
                    <flux:heading size="xl">{{ $this->totalUsersCount }}</flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    <flux:icon.shield-check class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-500 font-medium">{{ __('Owners (Full CRUD)') }}</flux:text>
                    <flux:heading size="xl">{{ $this->ownerCount }}</flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <flux:icon.briefcase class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-500 font-medium">{{ __('Management') }}</flux:text>
                    <flux:heading size="xl">{{ $this->managementCount }}</flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card class="!p-4">
            <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                    <flux:icon.user class="size-5" />
                </div>
                <div>
                    <flux:text class="text-xs text-zinc-500 font-medium">{{ __('Staff (Operational)') }}</flux:text>
                    <flux:heading size="xl">{{ $this->staffCount }}</flux:heading>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
        <div class="w-full sm:w-80">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search users by name or email...') }}"
                icon="magnifying-glass"
                clearable
            />
        </div>

        <div class="w-full sm:w-64">
            <flux:select wire:model.live="roleFilter" placeholder="{{ __('Filter by Role') }}">
                <flux:select.option value="">{{ __('All Roles') }}</flux:select.option>
                <flux:select.option value="Owner Integrated Personal System">{{ __('Owner Integrated Personal System') }}</flux:select.option>
                <flux:select.option value="Management Integrated Personal System">{{ __('Management Integrated Personal System') }}</flux:select.option>
                <flux:select.option value="Staff Integrated Personal System">{{ __('Staff Integrated Personal System') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    {{-- Users Table --}}
    <flux:card class="!p-0 overflow-hidden shadow-xs">
        <flux:table class="w-full">
            <flux:table.columns>
                <flux:table.column class="!ps-6 !py-4">{{ __('User') }}</flux:table.column>
                <flux:table.column class="!px-4 !py-4">{{ __('Role') }}</flux:table.column>
                <flux:table.column class="!px-4 !py-4">{{ __('Access Scope') }}</flux:table.column>
                <flux:table.column class="!px-4 !py-4">{{ __('Joined Date') }}</flux:table.column>
                <flux:table.column class="!pe-6 !py-4" align="end">{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->users as $user)
                    @php
                        $userRole = $user->roles->first()?->name ?? ($user->email === 'owner@gmail.com' ? 'Owner Integrated Personal System' : 'No Role');
                        $isCurrentUser = $user->id === auth()->id();
                    @endphp
                    <flux:table.row :key="$user->id" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                        {{-- User Column --}}
                        <flux:table.cell class="!ps-6 !py-4">
                            <div class="flex items-center gap-3.5">
                                <div class="flex size-10 items-center justify-center rounded-full bg-accent-content text-accent-foreground font-semibold text-xs shrink-0 shadow-xs">
                                    {{ $user->initials() }}
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100 truncate">
                                            {{ $user->full_name ?? $user->name }}
                                        </span>
                                        @if ($isCurrentUser)
                                            <span class="inline-flex items-center rounded-sm bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 text-[10px] font-medium text-zinc-600 dark:text-zinc-400">
                                                {{ __('You') }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-zinc-500 truncate">{{ $user->email }}</span>
                                </div>
                            </div>
                        </flux:table.cell>

                        {{-- Role Column --}}
                        <flux:table.cell class="!px-4 !py-4">
                            @if ($userRole === 'Owner Integrated Personal System')
                                <flux:badge color="purple" size="sm" inset="top bottom">
                                    <flux:icon.shield-check class="size-3.5 mr-1" />
                                    {{ __('Owner') }}
                                </flux:badge>
                            @elseif ($userRole === 'Management Integrated Personal System')
                                <flux:badge color="blue" size="sm" inset="top bottom">
                                    <flux:icon.briefcase class="size-3.5 mr-1" />
                                    {{ __('Management') }}
                                </flux:badge>
                            @elseif ($userRole === 'Staff Integrated Personal System')
                                <flux:badge color="zinc" size="sm" inset="top bottom">
                                    <flux:icon.user class="size-3.5 mr-1" />
                                    {{ __('Staff') }}
                                </flux:badge>
                            @else
                                <flux:badge color="gray" size="sm" inset="top bottom">{{ $userRole }}</flux:badge>
                            @endif
                        </flux:table.cell>

                        {{-- Access Scope Description --}}
                        <flux:table.cell class="!px-4 !py-4">
                            @if ($userRole === 'Owner Integrated Personal System')
                                <p class="text-xs truncate font-medium text-purple-700 dark:text-purple-400" title="{{ __('Full CRUD (Projects, Tasks, Users, Settings)') }}">
                                    {{ __('Full CRUD (Projects, Tasks, Users, Settings)') }}
                                </p>
                            @elseif ($userRole === 'Management Integrated Personal System')
                                <p class="text-xs truncate text-zinc-600 dark:text-zinc-400" title="{{ __('Managerial CRUD (Projects, Tasks, Team)') }}">
                                    {{ __('Managerial CRUD (Projects, Tasks, Team)') }}
                                </p>
                            @elseif ($userRole === 'Staff Integrated Personal System')
                                <p class="text-xs truncate text-zinc-600 dark:text-zinc-400" title="{{ __('Operational (View Projects, Manage Tasks)') }}">
                                    {{ __('Operational (View Projects, Manage Tasks)') }}
                                </p>
                            @else
                                <p class="text-xs text-zinc-400">-</p>
                            @endif
                        </flux:table.cell>

                        {{-- Joined Date --}}
                        <flux:table.cell class="!px-4 !py-4 text-xs text-zinc-500">
                            {{ $user->created_at ? $user->created_at->format('M d, Y') : '-' }}
                        </flux:table.cell>

                        {{-- Actions Column --}}
                        <flux:table.cell class="!pe-6 !py-4" align="end">
                            <div class="flex items-center justify-end gap-1.5">
                                @can('IPS - UAM - Can Update Users')
                                    <flux:button
                                        variant="ghost"
                                        size="sm"
                                        icon="pencil-square"
                                        wire:click="openEditModal({{ $user->id }})"
                                        {{-- tooltip="{{ __('Edit User & Role') }}" --}}
                                    />
                                @endcan

                                @can('IPS - UAM - Can Delete Users')
                                    @if (! $isCurrentUser)
                                        <flux:button
                                            variant="ghost"
                                            size="sm"
                                            icon="trash"
                                            class="text-red-600 hover:text-red-700 dark:text-red-400"
                                            wire:click="confirmDelete({{ $user->id }})"
                                            {{-- tooltip="{{ __('Delete User') }}" --}}
                                        />
                                    @endif
                                @endcan
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center !py-12 text-zinc-500">
                            <flux:icon.users class="size-8 mx-auto mb-2 text-zinc-400" />
                            <p class="font-medium text-sm">{{ __('No users found.') }}</p>
                            <p class="text-xs mt-1">{{ __('Try adjusting your search or role filters.') }}</p>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>


        {{-- Pagination --}}
        @if ($this->users->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $this->users->links() }}
            </div>
        @endif
    </flux:card>

    {{-- MODAL: CREATE USER --}}
    <flux:modal wire:model="showCreateModal" class="md:w-lg">
        <form wire:submit="createUser" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('Create New User') }}</flux:heading>
                <flux:subheading>{{ __('Add a new user and assign their system access role.') }}</flux:subheading>
            </div>

            <flux:input
                wire:model="fullName"
                :label="__('Full Name')"
                placeholder="e.g. John Doe"
                required
            />

            <flux:input
                wire:model="email"
                type="email"
                :label="__('Email Address')"
                placeholder="john@example.com"
                required
            />

            <flux:input
                wire:model="password"
                type="password"
                :label="__('Password')"
                placeholder="Minimum 8 characters"
                viewable
                required
            />

            <flux:select wire:model="selectedRole" :label="__('System Access Role')" required>
                @foreach ($this->availableRoles as $role)
                    <flux:select.option :value="$role->name">
                        {{ $role->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Create User') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- MODAL: EDIT USER --}}
    <flux:modal wire:model="showEditModal" class="md:w-lg">
        <form wire:submit="updateUser" class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('Edit User & Access Role') }}</flux:heading>
                <flux:subheading>{{ __('Update profile information and manage assigned permissions.') }}</flux:subheading>
            </div>

            <flux:input
                wire:model="fullName"
                :label="__('Full Name')"
                required
            />

            <flux:input
                wire:model="email"
                type="email"
                :label="__('Email Address')"
                required
            />

            <flux:input
                wire:model="password"
                type="password"
                :label="__('New Password (Optional)')"
                placeholder="Leave blank to keep existing password"
                viewable
            />

            @can('IPS - UAM - Can Assign Roles')
                <flux:select wire:model="selectedRole" :label="__('System Access Role')" required>
                    @foreach ($this->availableRoles as $role)
                        <flux:select.option :value="$role->name">
                            {{ $role->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            @endcan

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">{{ __('Save Changes') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- MODAL: CONFIRM DELETE --}}
    <flux:modal wire:model="showDeleteModal" class="md:w-md">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg" class="text-red-600 dark:text-red-400">
                    {{ __('Confirm Delete User') }}
                </flux:heading>
                <flux:subheading>
                    {{ __('Are you sure you want to remove :user from the system? This action can be soft-recovered if needed.', ['user' => $userToDeleteName]) }}
                </flux:subheading>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button variant="danger" wire:click="deleteUser">
                    {{ __('Delete User') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
