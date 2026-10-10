<?php

namespace App\Http\Resources;

use App\Models\Portfolio\PortfolioJobDesc;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PortfolioJobDesc */
class PortfolioJobDescResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'job_desc_id' => $this->job_desc_id,
            'description' => $this->description,
            'learning_outcomes' => $this->learning_outcomes,
        ];
    }
}
