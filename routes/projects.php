<?php

use App\Livewire\Projects\Create;
use App\Livewire\Projects\Edit;
use App\Livewire\Projects\Index;
use App\Livewire\Projects\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::livewire('projects', Index::class)->name('projects.index');
    Route::livewire('projects/create', Create::class)->name('projects.create');
    Route::livewire('projects/{project}', Show::class)->name('projects.show');
    Route::livewire('projects/{project}/edit', Edit::class)->name('projects.edit');
});
