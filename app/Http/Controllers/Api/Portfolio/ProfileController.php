<?php

namespace App\Http\Controllers\Api\Portfolio;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortfolioProfileResource;
use App\Models\Portfolio\PortfolioProfile;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function show(): JsonResponse
    {
        $profile = PortfolioProfile::first();

        if (! $profile) {
            return response()->json(['data' => null, 'message' => 'Profile not found.'], 404);
        }

        return response()->json(['data' => new PortfolioProfileResource($profile)]);
    }
}
