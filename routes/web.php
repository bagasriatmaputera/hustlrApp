<?php

use App\Livewire\UAM\Index;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('uam', Index::class)->name('uam.index');
});

require __DIR__.'/projects.php';
require __DIR__.'/job-tracker.php';
require __DIR__.'/settings.php';
require __DIR__.'/portfolio.php';
