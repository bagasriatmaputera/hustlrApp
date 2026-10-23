@props([
    'label',
    'value',
    'icon' => 'chart-bar',
    'tone' => 'zinc',
])

@php
    $tones = [
        'zinc' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
        'blue' => 'bg-blue-100 text-blue-600 dark:bg-blue-950/50 dark:text-blue-300',
        'emerald' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-300',
        'amber' => 'bg-amber-100 text-amber-600 dark:bg-amber-950/50 dark:text-amber-300',
        'rose' => 'bg-rose-100 text-rose-600 dark:bg-rose-950/50 dark:text-rose-300',
        'purple' => 'bg-purple-100 text-purple-600 dark:bg-purple-950/50 dark:text-purple-300',
    ];
    $toneClasses = $tones[$tone] ?? $tones['zinc'];
@endphp

<flux:card class="!p-4">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <flux:text class="text-xs font-medium uppercase tracking-wide">{{ $label }}</flux:text>
            <flux:heading size="xl" class="mt-1 break-words">{{ $value }}</flux:heading>
        </div>

        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg {{ $toneClasses }}">
            <flux:icon name="{{ $icon }}" class="size-5" />
        </div>
    </div>
</flux:card>
