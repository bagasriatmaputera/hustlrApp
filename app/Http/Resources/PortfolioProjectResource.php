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
        $gallery = collect($this->media_gallery ?? [])
            ->filter(function (array|string $item): bool {
                if (is_string($item)) {
                    return trim($item) !== '';
                }

                $image = $item['image'] ?? $item['url'] ?? null;

                return is_string($image) && trim($image) !== '';
            })
            ->map(function (array|string $item): array {
                if (is_string($item)) {
                    return [
                        'image' => $item,
                        'image_url' => $this->mediaUrl($item),
                        'caption' => null,
                    ];
                }

                $image = (string) ($item['image'] ?? $item['url'] ?? '');

                $caption = null;
                if (isset($item['caption'])) {
                    $caption = [
                        'en' => $item['caption']['en'] ?? null,
                        'id' => $item['caption']['id'] ?? null,
                    ];
                } elseif (isset($item['caption_en']) || isset($item['caption_id'])) {
                    $caption = [
                        'en' => $item['caption_en'] ?? null,
                        'id' => $item['caption_id'] ?? null,
                    ];
                }

                return [
                    'image' => $image,
                    'image_url' => $this->mediaUrl($image),
                    'caption' => $caption,
                ];
            })
            ->values()
            ->all();

        return [
            'project_id' => $this->project_id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'image_url' => $this->mediaUrl($this->image),
            'gif_image' => $this->gif_image,
            'gif_image_url' => $this->mediaUrl($this->gif_image),
            'media_gallery' => $gallery,
            'problem_solution' => $this->problem_solution,
            'problem_and_solution' => $this->problem_solution,
            'challenges_learnings' => $this->challenges_learnings,
            'challenges_and_learnings' => $this->challenges_learnings,
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

        if (str_starts_with($path, 'portfolio/')) {
            return route('portfolio.media', ['path' => $path]);
        }

        $disk = (string) config('filesystems.default', 's3');

        return Storage::disk($disk)->url($path);
    }
}
