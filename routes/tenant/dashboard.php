<?php

use App\Application\Api\v1\Dashboard\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->group(function () {
    Route::get('/overview', [DashboardController::class, 'getOverview']);
    Route::get('/entries-vs-exits', [DashboardController::class, 'getEntriesVsExits']);
    Route::get('/member-engagement', [DashboardController::class, 'getMemberEngagement']);
});
