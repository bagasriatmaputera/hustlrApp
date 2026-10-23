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
            'resume_url' => $this->resume_url,
            'available_for_work' => $this->available_for_work,
            'socials' => $this->socials,
            'image_url' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
