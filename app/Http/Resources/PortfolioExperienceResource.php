<?php

namespace App\Http\Resources;

use App\Models\Portfolio\PortfolioExperience;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PortfolioExperience */
class PortfolioExperienceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'experience_id' => $this->experience_id,
            'company_name' => $this->company_name,
            'position' => $this->position,
            'description' => $this->description,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'is_current' => $this->end_date === null,
            'location' => $this->location,
            'workplace_type' => $this->workplace_type,
            'contract_type' => $this->contract_type,
            'order' => $this->order,
            'job_descs' => PortfolioJobDescResource::collection($this->whenLoaded('jobDescs')),
        ];
    }
}
