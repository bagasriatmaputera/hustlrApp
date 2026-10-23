<?php

namespace App\Http\Controllers\Api\Portfolio;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortfolioExperienceResource;
use App\Http\Resources\PortfolioProfileResource;
use App\Http\Resources\PortfolioProjectResource;
use App\Models\Portfolio\PortfolioExperience;
use App\Models\Portfolio\PortfolioProfile;
use App\Models\Portfolio\PortfolioProject;
use Illuminate\Http\JsonResponse;

class PortfolioController extends Controller
{
    public function show(): JsonResponse
    {
        $profile = PortfolioProfile::first();

        return response()->json([
            'data' => [
                'profile' => $profile ? new PortfolioProfileResource($profile) : null,
                'projects' => PortfolioProjectResource::collection(
                    PortfolioProject::orderBy('order')->get()
                ),
                'experiences' => PortfolioExperienceResource::collection(
                    PortfolioExperience::with('jobDescs')->orderBy('order')->get()
                ),
            ],
        ]);
    }
}
