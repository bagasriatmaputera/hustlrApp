<?php

namespace App\Http\Resources;

use App\Models\Portfolio\PortfolioProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin PortfolioProfile */
class PortfolioProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'profile_id' => $this->profile_id,
            'name' => $this->name,
            'job_position' => $this->job_position,
            'summary' => $this->summary,
            'location' => $this->location,
            'resume_url' => $this->resolveFileUrl($this->resume_url),
            'resume_path' => $this->resume_url,
            'available_for_work' => $this->available_for_work,
            'socials' => $this->socials,
            'image_path' => $this->image_path,
            'image_url' => $this->resolveFileUrl($this->image_path),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function resolveFileUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, '/') || str_starts_with($path, 'storage/')) {
            return asset(ltrim($path, '/'));
        }

        if (str_starts_with($path, 'portfolio/')) {
            return route('portfolio.media', ['path' => $path]);
        }

        $disk = (string) config('filesystems.default', 's3');

        return Storage::disk($disk)->url($path);
    }
}
