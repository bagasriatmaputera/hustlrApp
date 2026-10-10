<?php

namespace App\Http\Resources;

use App\Models\Portfolio\PortfolioProject;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PortfolioProject */
class PortfolioProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'project_id' => $this->project_id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'gif_image' => $this->gif_image,
            'tech_stack' => $this->tech_stack,
            'live_url' => $this->live_url,
            'repo_url' => $this->repo_url,
            'featured' => $this->featured,
            'is_live' => $this->is_live,
            'is_repo' => $this->is_repo,
            'order' => $this->order,
        ];
    }
}
