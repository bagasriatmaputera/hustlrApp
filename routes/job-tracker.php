<?php

use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobApplicationExportController;
use App\Livewire\JobTracker\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    // Livewire Full Page Route for Job Tracker
    Route::livewire('job-tracker', Index::class)->name('job-tracker.index');
    Route::livewire('job-tracker/create', Index::class)->name('job-tracker.create');

    Route::get('job-tracker/export/csv', [JobApplicationExportController::class, 'csv'])->name('job-tracker.export.csv');
    Route::get('job-tracker/export/pdf', [JobApplicationExportController::class, 'pdf'])->name('job-tracker.export.pdf');

    // Controller Resource Routes (supports both Web and API/JSON)
    Route::resource('jobs', JobApplicationController::class)->parameters([
        'jobs' => 'jobApplication',
    ])->names([
        'index' => 'jobs.index',
        'create' => 'jobs.create',
        'store' => 'jobs.store',
        'show' => 'jobs.show',
        'edit' => 'jobs.edit',
        'update' => 'jobs.update',
        'destroy' => 'jobs.destroy',
    ]);
});
