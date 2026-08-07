<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reporting
|--------------------------------------------------------------------------
*/

// ====================== REPORTS ======================
$prefix = 'reports';
Route::controller(ReportController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/progress', 'progress')->name('progress')->middleware("permission:$prefix.view");
        Route::get('/quiz', 'quiz')->name('quiz')->middleware("permission:$prefix.view");
        Route::get('/leaderboard', 'leaderboard')->name('leaderboard')->middleware("permission:$prefix.view");
        Route::get('/activity', 'activity')->name('activity')->middleware("permission:$prefix.view");
        Route::get('/export', 'export')->name('export')->middleware("permission:$prefix.view");
    });
