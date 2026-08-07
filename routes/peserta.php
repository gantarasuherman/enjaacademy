<?php

declare(strict_types=1);

use App\Http\Controllers\SpaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Learner area — served by the React SPA
|--------------------------------------------------------------------------
| The learner experience lives in frontend/ (React 19 + TypeScript). Laravel
| serves the built bundle and exposes the data through routes/api.php.
|
| The paths below mirror `frontend/src/app/routes/index.tsx` exactly. That is
| deliberate: a deep link like /app/learning/mod-vocab-core has to resolve on
| the server (so the bundle is delivered) *and* inside React Router (so the
| right screen renders). One set of URLs, two routers.
|
| The named routes exist so the dynamic menu can target them — a row in the
| `menus` table stores `peserta.learning.module` and MenuBuilder turns it into
| a real URL.
*/

Route::prefix('app')->name('peserta.')->group(function () {

    Route::get('/dashboard', SpaController::class)->name('dashboard');

    // ====================== LEARNING ======================
    $prefix = 'learning';
    Route::prefix($prefix)->name("$prefix.")->group(function () {
        Route::get('/', SpaController::class)->name('index');
        Route::get('/lesson/{lesson}', SpaController::class)->name('lesson');
        Route::get('/{module}', SpaController::class)->name('module');
    });

    // ====================== SKILL MODULES ======================
    foreach ([
        'vocabulary' => 'vocabulary',
        'grammar' => 'grammar',
        'listening' => 'listening',
        'speaking' => 'speaking',
        'reading' => 'reading',
        'writing' => 'writing',
        'conversation' => 'conversation',
    ] as $path => $name) {
        Route::get("/{$path}", SpaController::class)->name($name);
        Route::get("/{$path}/{item}", SpaController::class)->name("{$name}.show");
    }

    // ====================== PRACTICE ======================
    $prefix = 'quiz';
    Route::prefix($prefix)->name('quizzes.')->group(function () {
        Route::get('/', SpaController::class)->name('index');
        Route::get('/{quiz}', SpaController::class)->name('show');
        Route::get('/{quiz}/result', SpaController::class)->name('result');
    });

    $prefix = 'flashcard';
    Route::prefix($prefix)->name('flashcards.')->group(function () {
        Route::get('/', SpaController::class)->name('index');
        Route::get('/{deck}', SpaController::class)->name('show');
    });

    // ====================== ENGAGEMENT ======================
    Route::get('/bookmark', SpaController::class)->name('bookmarks.index');
    Route::get('/progress', SpaController::class)->name('progress');
    Route::get('/achievement', SpaController::class)->name('achievements');
    Route::get('/leaderboard', SpaController::class)->name('leaderboard');
    Route::get('/certificate', SpaController::class)->name('certificates');

    // ====================== ACCOUNT ======================
    Route::get('/profile', SpaController::class)->name('profile.edit');
    Route::get('/setting', SpaController::class)->name('settings');

    // Registered last so the named routes above win. Catches the SPA's deeper
    // paths (/app/grammar/tenses/simple-past, …) which have no menu target and
    // therefore need no name of their own.
    Route::get('/{any}', SpaController::class)->where('any', '.*')->name('spa');
});
