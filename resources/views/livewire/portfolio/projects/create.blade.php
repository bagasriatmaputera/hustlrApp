<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center gap-3">
        <flux:button variant="ghost" icon="arrow-left" :href="route('portfolio.projects.index')" wire:navigate />
        <div>
            <flux:heading size="xl">{{ __('Add Portfolio Project') }}</flux:heading>
            <flux:subheading>{{ __('Add a new project to your portfolio.') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="flex w-full flex-col gap-6">
        <flux:card class="flex flex-col gap-6">
            <flux:heading size="lg">{{ __('Project Title') }}</flux:heading>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="title_en" :label="__('Title (EN)')" :placeholder="__('Project name in English')" required />
                <flux:input wire:model="title_id" :label="__('Title (ID)')" :placeholder="__('Nama project dalam Bahasa Indonesia')" required />
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-6">
            <flux:heading size="lg">{{ __('Description') }}</flux:heading>
            <flux:textarea wire:model="description_en" :label="__('Description (EN)')" :placeholder="__('Project description in English...')" rows="3" required />
            <flux:textarea wire:model="description_id" :label="__('Description (ID)')" :placeholder="__('Deskripsi project dalam Bahasa Indonesia...')" rows="3" required />
        </flux:card>

        <flux:card class="flex flex-col gap-6">
            <flux:heading size="lg">{{ __('Media & Links') }}</flux:heading>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="image" :label="__('Image Path')" :placeholder="__('/images/projects/preview.jpeg')" />
                <flux:input wire:model="gif_image" :label="__('GIF Image Path')" :placeholder="__('/images/projects/preview.gif')" />
                <flux:input wire:model="live_url" :label="__('Live URL')" :placeholder="__('https://example.com')" type="url" />
                <flux:input wire:model="repo_url" :label="__('Repository URL')" :placeholder="__('https://github.com/...')" type="url" />
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-6">
            <flux:heading size="lg">{{ __('Tech Stack & Meta') }}</flux:heading>
            <flux:input wire:model="tech_stack_raw" :label="__('Tech Stack (comma separated)')" :placeholder="__('Laravel, Vue.js, Tailwind CSS, MySQL')" required />
            <flux:input wire:model="order" :label="__('Display Order')" type="number" min="0" />

            <div class="grid gap-4 sm:grid-cols-3">
                <flux:checkbox wire:model="featured" :label="__('Featured')" />
                <flux:checkbox wire:model="is_live" :label="__('Is Live')" />
                <flux:checkbox wire:model="is_repo" :label="__('Has Repository')" />
            </div>
        </flux:card>

        <div class="flex items-center justify-end gap-3">
            <flux:button variant="ghost" :href="route('portfolio.projects.index')" wire:navigate>
                {{ __('Cancel') }}
            </flux:button>
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled">
                {{ __('Create Project') }}
            </flux:button>
        </div>
    </form>
</div>
