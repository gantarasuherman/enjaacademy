<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GrammarController;
use App\Http\Controllers\Api\LearningController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\QuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API (Sanctum)
|--------------------------------------------------------------------------
| Registered with the `api` prefix and the `api.` name prefix by
| bootstrap/app.php.
*/

// ====================== PUBLIC ======================
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:6,1')
    ->name('login');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:6,1')
    ->name('register');

// ====================== AUTHENTICATED ======================
Route::middleware('auth:sanctum')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::get('/me', 'me')->name('me');
        Route::post('/logout', 'logout')->name('logout');
        Route::post('/logout-all', 'logoutAll')->name('logout-all');
    });

    // ====================== DYNAMIC MENU ======================
    $prefix = 'menus';
    Route::controller(MenuController::class)
        ->prefix($prefix)
        ->name("$prefix.")
        ->group(function () {
            Route::get('/', 'all')->name('all');
            Route::get('/{position}', 'index')->name('index');
        });

    // ====================== LEARNING ======================
    $prefix = 'learning';
    Route::controller(LearningController::class)
        ->prefix($prefix)
        ->name("$prefix.")
        ->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/modules', 'modules')->name('modules');
            Route::post('/modules/{moduleSlug}/enroll', 'toggleEnrollment')->name('modules.toggle-enrollment');
            Route::get('/modules/{moduleSlug}/lessons', 'lessons')->name('lessons');
            Route::get('/lessons/{lesson}', 'lesson')->name('lesson');
            Route::post('/lessons/{lesson}/complete', 'complete')->name('complete');
        });

    // ====================== QUIZZES ======================
    $prefix = 'quizzes';
    Route::controller(QuizController::class)
        ->prefix($prefix)
        ->name("$prefix.")
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/history', 'history')->name('history');
            Route::get('/{quiz}', 'show')->name('show');
            Route::get('/{quiz}/questions', 'questions')->name('questions');
            Route::get('/{quiz}/attempts', 'attempts')->name('attempts');
            Route::post('/{quiz}/submit', 'submit')->name('submit');
        });

    // ====================== GRAMMAR ======================
    $prefix = 'grammar';
    Route::controller(GrammarController::class)
        ->prefix($prefix)
        ->name("$prefix.")
        ->group(function () {
            Route::get('/levels', 'levels')->name('levels');
            Route::get('/categories/{grammar_category}/patterns', 'patterns')->name('patterns');
            Route::get('/patterns/{grammar_pattern}', 'show')->name('show');
        });
});
