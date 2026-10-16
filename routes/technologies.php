<?php

use App\Livewire\Technologies\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'approved'])->group(function () {
    Route::livewire('technologies', Index::class)->name('technologies.index');
});
