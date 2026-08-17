<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\DailyQuizController;
use App\Http\Controllers\Api\GrammarController;
use App\Http\Controllers\Api\LearningController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\PublicCatalogController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\SkillPracticeController;
use App\Http\Controllers\Api\VocabularyWordController;
use App\Http\Controllers\Api\WeakWordController;
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

Route::get('/public/modules', [PublicCatalogController::class, 'modules'])
    ->middleware('throttle:30,1')
    ->name('public.modules');

Route::get('/public/modules/{moduleSlug}', [PublicCatalogController::class, 'module'])
    ->middleware('throttle:30,1')
    ->name('public.modules.show');

// Tripay's payment-status callback — verified via HMAC signature (see
// PaymentWebhookController), not Sanctum; a webhook carries no user session.
Route::post('/webhooks/tripay', [PaymentWebhookController::class, 'handle'])
    ->middleware('throttle:60,1')
    ->name('webhooks.tripay');

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
            Route::post('/modules/{moduleSlug}/checkout', [OrderController::class, 'checkout'])->name('modules.checkout');
            Route::post('/modules/{moduleSlug}/set-active', 'setActiveModule')->name('modules.set-active');
            Route::get('/modules/{moduleSlug}/lessons', 'lessons')->name('lessons');
            Route::get('/lessons/{lesson}', 'lesson')->name('lesson');
            Route::post('/lessons/{lesson}/complete', 'complete')->name('complete');
        });

    // ====================== ORDERS (checkout / QRIS payment) ======================
    $prefix = 'orders';
    Route::controller(OrderController::class)
        ->prefix($prefix)
        ->name("$prefix.")
        ->group(function () {
            Route::post('/{order}/simulate-payment', 'simulatePayment')->name('simulate-payment');
            Route::get('/{order}/check-status', 'checkStatus')->name('check-status');
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
            Route::get('/categories/{grammar_category}', 'showCategory')->name('categories.show');
            Route::get('/categories/{grammar_category}/patterns', 'patterns')->name('patterns');
            Route::get('/patterns/{grammar_pattern}', 'show')->name('show');
        });

    // ====================== DAILY QUIZ / VOCABULARY BANK ======================
    Route::controller(DailyQuizController::class)
        ->prefix('daily-quiz')
        ->name('daily-quiz.')
        ->group(function () {
            Route::get('/status', 'status')->name('status');
            Route::get('/', 'show')->name('show');
            Route::post('/submit', 'submit')->name('submit');
            Route::post('/skip', 'skip')->name('skip');
        });

    Route::get('/weak-words', [WeakWordController::class, 'index'])->name('weak-words');
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates');

    Route::controller(VocabularyWordController::class)
        ->prefix('vocabulary-words')
        ->name('vocabulary-words.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/progress', 'progress')->name('progress');
        });

    Route::controller(SkillPracticeController::class)
        ->prefix('skill-practice')
        ->name('skill-practice.')
        ->group(function () {
            Route::post('/speaking/{item}/complete', 'completeSpeaking')->name('speaking.complete');
            Route::post('/writing/{lesson}/submit', 'submitWriting')->name('writing.submit');
        });
});
