<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Portfolio Profile') }}</flux:heading>
            <flux:subheading>{{ __('Manage your public portfolio profile information.') }}</flux:subheading>
        </div>
    </div>

    <form wire:submit="save" class="flex w-full flex-col gap-6">
        <flux:card class="flex flex-col gap-6 !p-4 sm:!p-6">
            <flux:heading size="lg">{{ __('Profile Image') }}</flux:heading>

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <div class="shrink-0">
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" alt="Profile Preview" class="size-24 rounded-full object-cover ring-2 ring-emerald-500 shadow-sm" />
                    @elseif ($currentImagePath)
                        @php
                            $profileImageUrl = filter_var($currentImagePath, FILTER_VALIDATE_URL)
                                ? $currentImagePath
                                : (str_starts_with($currentImagePath, '/') || str_starts_with($currentImagePath, 'storage/')
                                    ? asset(ltrim($currentImagePath, '/'))
                                    : (str_starts_with($currentImagePath, 'portfolio/')
                                        ? route('portfolio.media', ['path' => $currentImagePath])
                                        : Storage::disk((string) config('filesystems.default', 's3'))->url($currentImagePath)));
                        @endphp
                        <img src="{{ $profileImageUrl }}" alt="Profile" class="size-24 rounded-full object-cover ring-2 ring-zinc-200 dark:ring-zinc-700" />
                    @else
                        <div class="flex size-24 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <flux:icon.user class="size-10 text-zinc-400" />
                        </div>
                    @endif
                </div>

                <div class="flex w-full flex-1 flex-col gap-2">
                    <flux:input wire:model="image" type="file" accept="image/*" :label="__('Upload New Photo')" class="w-full" />
                    <flux:text class="text-xs">{{ __('Max 2MB. JPG, PNG, GIF allowed.') }}</flux:text>
                    @if ($currentImagePath)
                        <div class="pt-1">
                            <flux:button variant="danger" size="sm" wire:click="removeImage" wire:confirm="{{ __('Remove profile image?') }}" class="w-full sm:w-auto">
                                {{ __('Remove Image') }}
                            </flux:button>
                        </div>
                    @endif
                </div>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-6 !p-4 sm:!p-6">
            <flux:heading size="lg">{{ __('Basic Information') }}</flux:heading>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="name" :label="__('Full Name')" :placeholder="__('e.g. Bagas Riatmaputera')" required />
                <flux:input wire:model="job_position" :label="__('Job Position')" :placeholder="__('e.g. Software Engineer')" required />
            </div>
            <flux:textarea wire:model="summary" :label="__('Summary / Bio')" :placeholder="__('Write a short professional summary...')" rows="5" required />
        </flux:card>

        <flux:card class="flex flex-col gap-6 !p-4 sm:!p-6">
            <flux:heading size="lg">{{ __('Resume / Curriculum Vitae (CV)') }}</flux:heading>

            <div class="flex flex-col gap-4">
                @if ($resume_url)
                    @php
                        $resolvedResumeUrl = filter_var($resume_url, FILTER_VALIDATE_URL)
                            ? $resume_url
                            : (str_starts_with($resume_url, '/') || str_starts_with($resume_url, 'storage/')
                                ? asset(ltrim($resume_url, '/'))
                                : (str_starts_with($resume_url, 'portfolio/')
                                    ? route('portfolio.media', ['path' => $resume_url])
                                    : Storage::disk((string) config('filesystems.default', 's3'))->url($resume_url)));
                    @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-800 dark:bg-zinc-900/50">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                <flux:icon.document-text class="size-6" />
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ basename($resume_url) }}
                                </span>
                                <span class="text-xs text-zinc-500">
                                    {{ __('Active Resume File / Link') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end shrink-0">
                            <flux:button variant="ghost" size="sm" icon="arrow-top-right-on-square" :href="$resolvedResumeUrl" target="_blank" class="flex-1 sm:flex-none">
                                {{ __('Preview / View CV') }}
                            </flux:button>
                            <flux:button variant="danger" size="sm" icon="trash" wire:click="removeResume" wire:confirm="{{ __('Are you sure you want to remove this resume?') }}" class="flex-1 sm:flex-none">
                                {{ __('Remove') }}
                            </flux:button>
                        </div>
                    </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input wire:model="resume_file" type="file" accept=".pdf,.doc,.docx,application/pdf" :label="__('Upload Resume / CV File (S3)')" class="w-full" />
                    <flux:input wire:model="resume_url" :label="__('Or Resume URL (External / Drive)')" :placeholder="__('https://example.com/resume.pdf')" class="w-full" />
                </div>
                <flux:text class="text-xs text-zinc-500">{{ __('Supported file types: PDF, DOC, DOCX up to 10MB.') }}</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex flex-col gap-6 !p-4 sm:!p-6">
            <flux:heading size="lg">{{ __('Contact & Availability') }}</flux:heading>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:input wire:model="location" :label="__('Location')" :placeholder="__('e.g. Jakarta, Indonesia')" />
                <div class="flex items-center pt-1 sm:pt-7">
                    <flux:checkbox wire:model="available_for_work" :label="__('Available for Work')" />
                </div>
            </div>

            <flux:heading size="md">{{ __('Social Media') }}</flux:heading>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <flux:input wire:model="socials.github" :label="__('GitHub URL')" :placeholder="__('https://github.com/username')" />
                <flux:input wire:model="socials.linkedin" :label="__('LinkedIn URL')" :placeholder="__('https://linkedin.com/in/username')" />
                <flux:input wire:model="socials.email" :label="__('Email')" :placeholder="__('name@example.com')" />
                <flux:input wire:model="socials.instagram" :label="__('Instagram URL')" :placeholder="__('https://www.instagram.com/username')" />
                <flux:input wire:model="socials.tiktok" :label="__('TikTok URL')" :placeholder="__('https://www.tiktok.com/@username')" />
            </div>
        </flux:card>

        <div class="flex items-center justify-end gap-3 pb-6">
            <flux:button variant="primary" type="submit" class="w-full sm:w-auto" wire:loading.attr="disabled">
                {{ __('Save Profile') }}
            </flux:button>
        </div>
    </form>
</div>
