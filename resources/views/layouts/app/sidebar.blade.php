<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 transition-all duration-300">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    @can('IPS - UAM - Can View Users in Sidebar')
                        <flux:sidebar.item icon="users" :href="route('uam.index')" :current="request()->routeIs('uam.*')" wire:navigate>
                            {{ __('UAM') }}
                        </flux:sidebar.item>
                    @endcan

                    <flux:sidebar.group icon="folder-git-2" :heading="__('Project')" expandable :expanded="request()->routeIs('projects.*')">
                        <flux:sidebar.item :href="route('projects.index')" :current="request()->routeIs('projects.index')" wire:navigate>
                            {{ __('All Projects') }}
                        </flux:sidebar.item>
                        @can('IPS - PROJECT - Can Create Projects')
                            <flux:sidebar.item :href="route('projects.create')" :current="request()->routeIs('projects.create')" wire:navigate>
                                {{ __('Add Project') }}
                            </flux:sidebar.item>
                        @endcan
                    </flux:sidebar.group>

                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            {{-- <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav> --}}

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Header Bar with Sidebar Toggle -->
        <flux:header class="border-b border-zinc-200/80 bg-zinc-50/50 dark:border-zinc-700/80 dark:bg-zinc-900/50">
            <flux:sidebar.toggle icon="bars-2" inset="left" />

            <flux:spacer />

            <div class="flex items-center gap-2">
                <flux:button variant="ghost" size="sm" icon="moon" x-data x-on:click="$flux.dark = !$flux.dark" />

                <!-- Mobile Profile Menu -->
                <div class="lg:hidden">
                    <flux:dropdown position="top" align="end">
                        <flux:profile
                            :initials="auth()->user()->initials()"
                            icon-trailing="chevron-down"
                        />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->full_name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->full_name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </div>
    </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
