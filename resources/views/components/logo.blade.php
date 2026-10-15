@props([
    'mode' => 'auto', // pilihan: auto, Light, Dark
    'path' => null,
    'size' => 'h-16 w-auto',
    'align' => 'center', // pilihan: left, center, right, top, bottom, center-all
    'alt' => config('app.name', 'Logo')
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'justify-start items-center',
        'right' => 'justify-end items-center',
        'top' => 'justify-center items-start',
        'bottom' => 'justify-center items-end',
        'top-left' => 'justify-start items-start',
        'top-right' => 'justify-end items-start',
        'bottom-left' => 'justify-start items-end',
        'bottom-right' => 'justify-end items-end',
        default => 'justify-center items-center',
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex w-full h-full ' . $alignmentClasses]) }}>
    @if ($path)
        {{-- Path kustom --}}
        <img src="{{ asset($path) }}" alt="{{ $alt }}" class="{{ $size }} object-contain transition-all" />
    @elseif ($mode === 'Dark')
        {{-- Paksa tampilkan logo terang saja --}}
        <img src="{{ asset('images/Hustlr Dark Logo.png') }}" alt="{{ $alt }}" class="{{ $size }} object-contain transition-all" />
    @elseif ($mode === 'Light')
        {{-- Paksa tampilkan logo gelap saja --}}
        <img src="{{ asset('images/Hustler Light Logo.png') }}" alt="{{ $alt }}" class="{{ $size }} object-contain transition-all" />
    @else
        {{-- Default ('auto'): Berganti otomatis mengikuti mode tema aktif --}}
        <img src="{{ asset('images/Hustlr Light Logo.png') }}" alt="{{ $alt }}" class="{{ $size }} object-contain transition-all dark:hidden" />
        <img src="{{ asset('images/Hustlr Dark Logo.png') }}" alt="{{ $alt }}" class="{{ $size }} object-contain transition-all hidden dark:block" />
    @endif
</div>