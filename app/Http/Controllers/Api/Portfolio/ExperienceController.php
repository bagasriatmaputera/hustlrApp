<?php

namespace App\Http\Controllers\Api\Portfolio;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortfolioExperienceResource;
use App\Models\Portfolio\PortfolioExperience;
use Illuminate\Http\JsonResponse;

class ExperienceController extends Controller
{
    public function index(): JsonResponse
    {
        $experiences = PortfolioExperience::with('jobDescs')->orderBy('order')->get();

        return response()->json(['data' => PortfolioExperienceResource::collection($experiences)]);
    }

    public function show(PortfolioExperience $portfolioExperience): JsonResponse
    {
        $portfolioExperience->load('jobDescs');

        return response()->json(['data' => new PortfolioExperienceResource($portfolioExperience)]);
    }
}
