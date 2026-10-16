@props([
    'technologies',
    'selected' => [],
])

@if ($technologies->isNotEmpty())
    @php
        $groupedTechnologies = $technologies->groupBy('category');
    @endphp

    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
        <button
            type="button"
            @click="open = !open"
            class="flex w-full items-center justify-between gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-start text-sm text-zinc-700 shadow-xs transition hover:border-zinc-300 dark:border-white/10 dark:bg-white/10 dark:text-zinc-300"
        >
            <span class="truncate">
                {{ empty($selected) ? __('Select technologies...') : count($selected).' '.__('selected') }}
            </span>
            <flux:icon.chevron-down class="size-4 shrink-0 text-zinc-400" />
        </button>

        <div
            x-show="open"
            x-cloak
            class="absolute z-50 mt-1 max-h-72 w-full overflow-auto rounded-lg border border-zinc-200 bg-white p-1.5 shadow-lg dark:border-zinc-700 dark:bg-zinc-800"
        >
            @foreach ($groupedTechnologies as $category => $techs)
                <div class="px-2 pt-2 pb-1 text-[11px] font-semibold uppercase tracking-wider text-zinc-400">
                    {{ $category }}
                </div>
                @foreach ($techs as $tech)
                    <label
                        wire:key="tech-option-{{ $tech->tech_id }}"
                        class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-sm text-zinc-700 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-700/50"
                    >
                        <input
                            type="checkbox"
                            wire:model.live="selectedTechnologies"
                            value="{{ $tech->tech_id }}"
                            class="size-4 rounded border-zinc-300 text-primary focus:ring-primary"
                        />
                        <span>{{ $tech->tech_name }}</span>
                    </label>
                @endforeach
            @endforeach
        </div>
    </div>

    @if (! empty($selected))
        <div class="flex flex-wrap gap-2">
            @foreach ($technologies->whereIn('tech_id', $selected) as $tech)
                <span
                    wire:key="tech-chip-{{ $tech->tech_id }}"
                    class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                >
                    {{ $tech->tech_name }}
                    <button
                        type="button"
                        wire:click="removeTechnology('{{ $tech->tech_id }}')"
                        class="text-blue-400 transition hover:text-red-500"
                    >
                        <flux:icon.x-mark class="size-3" />
                    </button>
                </span>
            @endforeach
        </div>
    @endif
@else
    <flux:text class="text-sm text-zinc-500">{{ __('No technologies available. Add technologies first.') }}</flux:text>
@endif
