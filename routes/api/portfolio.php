<?php

use App\Http\Controllers\Api\Portfolio\ExperienceController;
use App\Http\Controllers\Api\Portfolio\PortfolioController;
use App\Http\Controllers\Api\Portfolio\ProfileController;
use App\Http\Controllers\Api\Portfolio\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'show'])->name('api.portfolio.index');

Route::get('profile', [ProfileController::class, 'show']);

Route::get('projects', [ProjectController::class, 'index']);
Route::get('projects/{portfolioProject}', [ProjectController::class, 'show']);

Route::get('experiences', [ExperienceController::class, 'index']);
Route::get('experiences/{portfolioExperience}', [ExperienceController::class, 'show']);
