<?php

namespace App\Livewire\Portfolio\Profile;

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
    use WithFileUploads;

    public string $name = '';

    public string $job_position = '';

    public string $summary = '';

    #[Validate(['nullable', 'image', 'max:2048'])]
    public mixed $image = null;

    public ?string $currentImagePath = null;

    public function mount(): void
    {
        $profile = PortfolioProfile::first();

        if ($profile) {
            $this->name = $profile->name;
            $this->job_position = $profile->job_position;
            $this->summary = $profile->summary;
            $this->currentImagePath = $profile->image_path;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'job_position' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = [
            'name' => $this->name,
            'job_position' => $this->job_position,
            'summary' => $this->summary,
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
