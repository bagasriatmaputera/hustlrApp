<?php

namespace App\Http\Controllers\Api\Portfolio;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortfolioProjectResource;
use App\Models\Portfolio\PortfolioProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $projects = PortfolioProject::query()
            ->when($request->boolean('featured'), fn ($query) => $query->where('featured', true))
            ->orderBy('order')
            ->get();

        return response()->json(['data' => PortfolioProjectResource::collection($projects)]);
    }

    public function show(PortfolioProject $portfolioProject): JsonResponse
    {
        return response()->json(['data' => new PortfolioProjectResource($portfolioProject)]);
    }
}
