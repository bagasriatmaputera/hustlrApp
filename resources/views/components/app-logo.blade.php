@props([
    'sidebar' => false,
    'theme' => null, // 'dark', 'light', atau null (otomatis mendeteksi tema aktif)
])

@php
    $isExplicitDark = $theme === 'dark';
    $isExplicitLight = $theme === 'light';
@endphp

@if($sidebar)
    <flux:sidebar.brand :name="config('app.name', 'Laravel')" {{ $attributes }}>
        <x-slot name="logo" class="flex items-center justify-center">
            @if($isExplicitDark)
                {{-- 1. Paksa Mode Dark jika prop theme="dark" --}}
                <img src="{{ asset('images/Hustler Dark Logo.png') }}" alt="{{ config('app.name', 'Hustlr') }}" class="h-6 w-auto object-contain" />
            @elseif($isExplicitLight)
                {{-- 2. Paksa Mode Light jika prop theme="light" --}}
                <img src="{{ asset('images/Hustlr Light Logo.png') }}" alt="{{ config('app.name', 'Hustlr') }}" class="h-6 w-auto object-contain" />
            @else
                {{-- 3. Otomatis Deteksi Tema Aktif (Flux UI / Tailwind Responsive) --}}
                {{-- Tampil saat Mode Light --}}
                <div class="dark:hidden flex items-center justify-center">
                    <img src="{{ asset('images/Hustlr Light Logo.png') }}" alt="{{ config('app.name', 'Hustlr') }}" class="h-6 w-auto object-contain" />
                </div>

                {{-- Tampil saat Mode Dark --}}
                <div class="hidden dark:flex items-center justify-center">
                    <img src="{{ asset('images/Hustler Dark Logo.png') }}" alt="{{ config('app.name', 'Hustlr') }}" class="h-6 w-auto object-contain" />
                </div>
            @endif
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="config('app.name', 'Laravel')" {{ $attributes }}>
        <x-slot name="logo" class="flex items-center justify-center">
            @if($isExplicitDark)
                {{-- 1. Paksa Mode Dark jika prop theme="dark" --}}
                <img src="{{ asset('images/Hustler Dark Logo.png') }}" alt="{{ config('app.name', 'Hustlr') }}" class="h-6 w-auto object-contain" />
            @elseif($isExplicitLight)
                {{-- 2. Paksa Mode Light jika prop theme="light" --}}
                <img src="{{ asset('images/Hustlr Light Logo.png') }}" alt="{{ config('app.name', 'Hustlr') }}" class="h-6 w-auto object-contain" />
            @else
                {{-- 3. Otomatis Deteksi Tema Aktif (Flux UI / Tailwind Responsive) --}}
                {{-- Tampil saat Mode Light --}}
                <div class="dark:hidden flex items-center justify-center">
                    <img src="{{ asset('images/Hustlr Light Logo.png') }}" alt="{{ config('app.name', 'Hustlr') }}" class="h-6 w-auto object-contain" />
                </div>

                {{-- Tampil saat Mode Dark --}}
                <div class="hidden dark:flex items-center justify-center">
                    <img src="{{ asset('images/Hustler Dark Logo.png') }}" alt="{{ config('app.name', 'Hustlr') }}" class="h-6 w-auto object-contain" />
                </div>
            @endif
        </x-slot>
    </flux:brand>
@endif

