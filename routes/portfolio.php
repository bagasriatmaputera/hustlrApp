<?php

use App\Livewire\Portfolio\Experiences\Create as ExperienceCreate;
use App\Livewire\Portfolio\Experiences\Edit as ExperienceEdit;
use App\Livewire\Portfolio\Experiences\Index as ExperienceIndex;
use App\Livewire\Portfolio\Profile\Edit;
use App\Livewire\Portfolio\Projects\Create as ProjectCreate;
use App\Livewire\Portfolio\Projects\Edit as ProjectEdit;
use App\Livewire\Portfolio\Projects\Index as ProjectIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::livewire('portfolio/profile', Edit::class)->name('portfolio.profile.edit');

    // Project
    Route::livewire('portfolio/projects', ProjectIndex::class)->name('portfolio.projects.index');
    Route::livewire('portfolio/projects/create', ProjectCreate::class)->name('portfolio.projects.create');
    Route::livewire('portfolio/projects/{portfolioProject}/edit', ProjectEdit::class)->name('portfolio.projects.edit');

    // Experience
    Route::livewire('portfolio/experience', ExperienceIndex::class)->name('portfolio.experiences.index');
    Route::livewire('portfolio/experience/create', ExperienceCreate::class)->name('portfolio.experiences.create');
    Route::livewire('portfolio/experience/{portfolioExperience}/edit', ExperienceEdit::class)->name('portfolio.experiences.edit');

});
