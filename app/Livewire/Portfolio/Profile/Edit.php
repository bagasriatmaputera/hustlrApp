<?php

namespace App\Livewire\Portfolio\Profile;

use App\Concerns\AuthorizesPermissions;
use App\Models\Portfolio\PortfolioProfile;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Portfolio - Edit Profile')]
class Edit extends Component
{
    use AuthorizesPermissions, WithFileUploads;

    public string $name = '';

    public string $job_position = '';

    public string $summary = '';

    public string $location = '';

    public string $resume_url = '';

    public bool $available_for_work = true;

    /** @var array{github: string, linkedin: string, email: string} */
    public array $socials = [
        'github' => '',
        'linkedin' => '',
        'email' => '',
    ];

    #[Validate(['nullable', 'image', 'max:2048'])]
    public mixed $image = null;

    public ?string $currentImagePath = null;

    public function mount(): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Profile');

        $profile = PortfolioProfile::first();

        if ($profile) {
            $this->name = $profile->name;
            $this->job_position = $profile->job_position;
            $this->summary = $profile->summary;
            $this->location = (string) ($profile->location ?? '');
            $this->resume_url = (string) ($profile->resume_url ?? '');
            $this->available_for_work = (bool) ($profile->available_for_work ?? true);
            $this->socials = [
                'github' => (string) ($profile->socials['github'] ?? ''),
                'linkedin' => (string) ($profile->socials['linkedin'] ?? ''),
                'email' => (string) ($profile->socials['email'] ?? ''),
            ];
            $this->currentImagePath = $profile->image_path;
        }
    }

    public function save(): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Profile');

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'job_position' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'resume_url' => ['nullable', 'string', 'max:255'],
            'available_for_work' => ['boolean'],
            'socials' => ['array'],
            'socials.github' => ['nullable', 'string', 'max:255'],
            'socials.linkedin' => ['nullable', 'string', 'max:255'],
            'socials.email' => ['nullable', 'string', 'email', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = [
            'name' => $this->name,
            'job_position' => $this->job_position,
            'summary' => $this->summary,
            'location' => $this->location ?: null,
            'resume_url' => $this->resume_url ?: null,
            'available_for_work' => $this->available_for_work,
            'socials' => $this->socials,
        ];

        if ($this->image) {
            if ($this->currentImagePath) {
                Storage::disk('public')->delete($this->currentImagePath);
            }
            $data['image_path'] = $this->image->store('portfolio/profile', 'public');
        }

        $profile = PortfolioProfile::first();

        if ($profile) {
            $profile->update($data);
        } else {
            PortfolioProfile::create($data);
        }

        $this->currentImagePath = $profile?->fresh()?->image_path ?? $data['image_path'] ?? null;
        $this->image = null;

        Flux::toast(variant: 'success', text: __('Profile updated successfully.'));
    }

    public function removeImage(): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Profile');

        $profile = PortfolioProfile::first();

        if ($profile && $profile->image_path) {
            Storage::disk('public')->delete($profile->image_path);
            $profile->update(['image_path' => null]);
            $this->currentImagePath = null;
        }

        Flux::toast(variant: 'success', text: __('Profile image removed.'));
    }

    public function render(): mixed
    {
        return view('livewire.portfolio.profile.edit');
    }
}
