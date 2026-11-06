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
            <div>
                <flux:heading size="lg">{{ __('Problem & Solution') }}</flux:heading>
                <flux:subheading>{{ __('Explain the problem statement and the architectural/product solution provided.') }}</flux:subheading>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:textarea
                    wire:model="problem_solution_en"
                    :label="__('Problem & Solution (EN)')"
                    :placeholder="__('Explain the problem and how this project solved it in English...')"
                    rows="4"
                />
                <flux:textarea
                    wire:model="problem_solution_id"
                    :label="__('Problem & Solution (ID)')"
                    :placeholder="__('Jelaskan permasalahan dan bagaimana proyek ini menyelesaikannya dalam Bahasa Indonesia...')"
                    rows="4"
                />
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-6">
            <div>
                <flux:heading size="lg">{{ __('Challenges & Learnings') }}</flux:heading>
                <flux:subheading>{{ __('Key technical hurdles faced and key takeaways or lessons learned.') }}</flux:subheading>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:textarea
                    wire:model="challenges_learnings_en"
                    :label="__('Challenges & Learnings (EN)')"
                    :placeholder="__('Share technical hurdles, bottlenecks, and key takeaways in English...')"
                    rows="4"
                />
                <flux:textarea
                    wire:model="challenges_learnings_id"
                    :label="__('Challenges & Learnings (ID)')"
                    :placeholder="__('Ceritakan tantangan teknis, kendala, dan pelajaran berharga dalam Bahasa Indonesia...')"
                    rows="4"
                />
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-6">
            <flux:heading size="lg">{{ __('Media & Links') }}</flux:heading>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="flex flex-col gap-3 rounded-xl border border-zinc-200 p-4 dark:border-zinc-800">
                    <flux:heading size="sm">{{ __('Cover Image') }}</flux:heading>
                    @if ($image_file)
                        <div class="aspect-video w-full overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ $image_file->temporaryUrl() }}" alt="Cover Preview" class="h-full w-full object-cover object-top ring-2 ring-emerald-500" />
                        </div>
                    @endif
                    <flux:input wire:model="image_file" type="file" accept="image/*" :label="__('Upload Cover to S3')" />
                    <flux:input wire:model="image" :label="__('Or Image Path / URL')" :placeholder="__('/images/projects/preview.jpeg or https://...')" />
                </div>

                <div class="flex flex-col gap-3 rounded-xl border border-zinc-200 p-4 dark:border-zinc-800">
                    <flux:heading size="sm">{{ __('GIF Preview') }}</flux:heading>
                    @if ($gif_image_file)
                        <div class="aspect-video w-full overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ $gif_image_file->temporaryUrl() }}" alt="GIF Preview" class="h-full w-full object-cover object-top ring-2 ring-emerald-500" />
                        </div>
                    @endif
                    <flux:input wire:model="gif_image_file" type="file" accept="image/gif,image/webp,video/mp4" :label="__('Upload GIF to S3')" />
                    <flux:input wire:model="gif_image" :label="__('Or GIF Path / URL')" :placeholder="__('/images/projects/preview.gif or https://...')" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="live_url" :label="__('Live URL')" :placeholder="__('https://example.com')" type="url" />
                <flux:input wire:model="repo_url" :label="__('Repository URL')" :placeholder="__('https://github.com/...')" type="url" />
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">{{ __('Media Gallery') }}</flux:heading>
                    <flux:subheading>{{ __('Add screenshots, diagrams, or additional project preview images.') }}</flux:subheading>
                </div>
                <flux:button variant="ghost" size="sm" icon="plus" wire:click="addMediaGalleryItem">
                    {{ __('Add Media') }}
                </flux:button>
            </div>

            @if (empty($media_gallery))
                <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                    {{ __('No media gallery items added yet. Click "Add Media" to add screenshots or previews.') }}
                </div>
            @else
                <div class="flex flex-col gap-4">
                    @foreach ($media_gallery as $i => $item)
                        <div wire:key="media-gallery-item-{{ $i }}" class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                            <div class="flex items-start justify-between gap-2 pb-3 border-b border-zinc-200 dark:border-zinc-800">
                                <span class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
                                    {{ __('Media Item') }} #{{ $i + 1 }}
                                </span>
                                <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" wire:click="removeMediaGalleryItem({{ $i }})" />
                            </div>

                            <div class="mt-3 flex flex-col gap-4">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <flux:input
                                        wire:model="media_gallery.{{ $i }}.file"
                                        type="file"
                                        accept="image/*"
                                        :label="__('Upload to S3')"
                                    />
                                    <flux:input
                                        wire:model="media_gallery.{{ $i }}.image"
                                        :label="__('Or Image Path / URL')"
                                        :placeholder="__('/images/projects/screenshot-1.png or https://...')"
                                    />
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <flux:input
                                        wire:model="media_gallery.{{ $i }}.caption_en"
                                        :label="__('Caption (EN) - Optional')"
                                        :placeholder="__('e.g. Dashboard overview')"
                                    />
                                    <flux:input
                                        wire:model="media_gallery.{{ $i }}.caption_id"
                                        :label="__('Caption (ID) - Opsional')"
                                        :placeholder="__('cth. Tampilan antarmuka dasbor')"
                                    />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
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

        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <flux:button variant="ghost" :href="route('portfolio.projects.index')" wire:navigate class="w-full sm:w-auto">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button variant="primary" type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto">
                {{ __('Create Project') }}
            </flux:button>
        </div>
    </form>
</div>
