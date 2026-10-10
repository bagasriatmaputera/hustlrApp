<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Portfolio Profile') }}</flux:heading>
            <flux:subheading>{{ __('Manage your public portfolio profile information.') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="flex w-full flex-col gap-6">
        <flux:card class="flex flex-col gap-6">
            <flux:heading size="lg">{{ __('Profile Image') }}</flux:heading>

            <div class="flex items-center gap-6">
                @if ($currentImagePath)
                    <img src="{{ asset('storage/' . $currentImagePath) }}" alt="Profile" class="size-24 rounded-full object-cover ring-2 ring-zinc-200 dark:ring-zinc-700" />
                @else
                    <div class="flex size-24 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                        <flux:icon.user class="size-10 text-zinc-400" />
                    </div>
                @endif

                <div class="flex flex-col gap-2">
                    <flux:input wire:model="image" type="file" accept="image/*" :label="__('Upload New Photo')" />
                    <flux:text class="text-xs">{{ __('Max 2MB. JPG, PNG, GIF allowed.') }}</flux:text>
                    @if ($currentImagePath)
                        <flux:button variant="danger" size="sm" wire:click="removeImage" wire:confirm="{{ __('Remove profile image?') }}">
                            {{ __('Remove Image') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-6">
            <flux:heading size="lg">{{ __('Basic Information') }}</flux:heading>

            <flux:input wire:model="name" :label="__('Full Name')" :placeholder="__('e.g. Bagas Riatmaputera')" required />
            <flux:input wire:model="job_position" :label="__('Job Position')" :placeholder="__('e.g. Software Engineer')" required />
            <flux:textarea wire:model="summary" :label="__('Summary / Bio')" :placeholder="__('Write a short professional summary...')" rows="5" required />
        </flux:card>

        <div class="flex items-center justify-end gap-3">
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                {{ __('Save Profile') }}
            </flux:button>
        </div>
    </form>
</div>
