<?php

namespace App\Http\Resources;

use App\Models\Portfolio\PortfolioProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PortfolioProfile */
class PortfolioProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'profile_id' => $this->profile_id,
            'name' => $this->name,
            'job_position' => $this->job_position,
            'summary' => $this->summary,
            'image_url' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
