<?php

use Illuminate\Support\Facades\Route;

Route::prefix('portfolio')->group(function () {
    require __DIR__.'/api/portfolio.php';
});
