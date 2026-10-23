<?php

namespace App\Http\Resources;

use App\Models\Portfolio\PortfolioProject;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin PortfolioProject */
class PortfolioProjectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'project_id' => $this->project_id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'image_url' => $this->mediaUrl($this->image),
            'gif_image' => $this->gif_image,
            'gif_image_url' => $this->mediaUrl($this->gif_image),
            'tech_stack' => $this->tech_stack,
            'live_url' => $this->live_url,
            'repo_url' => $this->repo_url,
            'featured' => $this->featured,
            'is_live' => $this->is_live,
            'is_repo' => $this->is_repo,
            'order' => $this->order,
        ];
    }

    private function mediaUrl(?string $path): ?string
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

        return Storage::disk('public')->url($path);
    }
}
