<?php

namespace App\Http\Controllers\Api\Portfolio;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortfolioProjectResource;
use App\Models\Portfolio\PortfolioProject;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = PortfolioProject::orderBy('order')->get();

        return response()->json(['data' => PortfolioProjectResource::collection($projects)]);
    }

    public function show(PortfolioProject $portfolioProject): JsonResponse
    {
        return response()->json(['data' => new PortfolioProjectResource($portfolioProject)]);
    }
}
