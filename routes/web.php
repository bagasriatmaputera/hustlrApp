<?php

use App\Livewire\Approvals\Index as ApprovalsIndex;
use App\Livewire\UAM\Index;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('approval/pending', 'approval-pending')
    ->middleware('auth')
    ->name('approval.pending');

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('uam', Index::class)->name('uam.index');
    Route::livewire('approvals', ApprovalsIndex::class)->name('approvals.index');
});

require __DIR__.'/projects.php';
require __DIR__.'/technologies.php';
require __DIR__.'/job-tracker.php';
require __DIR__.'/settings.php';
require __DIR__.'/portfolio.php';
