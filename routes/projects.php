<?php

use App\Http\Controllers\TaskAttachmentController;
use App\Livewire\Projects\Create;
use App\Livewire\Projects\Edit;
use App\Livewire\Projects\Index;
use App\Livewire\Projects\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::livewire('projects', Index::class)->name('projects.index');
    Route::livewire('projects/create', Create::class)->name('projects.create');
    Route::get('projects/{project}/tasks/{task}/attachments/{attachment}', [TaskAttachmentController::class, 'download'])
        ->name('tasks.attachments.download');
    Route::get('projects/{project}/tasks/{task}/attachments/{attachment}/preview', [TaskAttachmentController::class, 'preview'])
        ->name('tasks.attachments.preview');
    Route::livewire('projects/{project}', Show::class)->name('projects.show');
    Route::livewire('projects/{project}/edit', Edit::class)->name('projects.edit');
});
