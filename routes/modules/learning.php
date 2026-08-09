<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\GrammarCategoryController;
use App\Http\Controllers\Admin\GrammarLevelController;
use App\Http\Controllers\Admin\GrammarPatternController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\QuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Learning content authoring
|--------------------------------------------------------------------------
*/

// ====================== LESSONS ======================
$prefix = 'lessons';
Route::controller(LessonController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::get('/template', 'template')->name('template')->middleware("permission:$prefix.create");
        Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
        Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
        Route::post('/reorder', 'reorder')->name('reorder')->middleware("permission:$prefix.update");
        Route::post('/ai/generate-items', 'generateItems')->name('ai.generate-items')->middleware("permission:$prefix.create");
        Route::post('/ai/generate-translation', 'generateTranslation')->name('ai.generate-translation')->middleware("permission:$prefix.create");
        Route::get('/{lesson}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
        Route::put('/{lesson}', 'update')->name('update')->middleware("permission:$prefix.update");
        Route::delete('/{lesson}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
        Route::post('/{lesson}/import', 'import')->name('import')->middleware("permission:$prefix.create");
        Route::get('/{lesson}/export', 'export')->name('export')->middleware("permission:$prefix.view");
    });

// ====================== QUIZZES ======================
$prefix = 'quizzes';
Route::controller(QuizController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
        Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
        Route::get('/{quiz}', 'show')->name('show')->middleware("permission:$prefix.view");
        Route::get('/{quiz}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
        Route::put('/{quiz}', 'update')->name('update')->middleware("permission:$prefix.update");
        Route::delete('/{quiz}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
    });

// ====================== GRAMMAR ======================
Route::prefix('grammar')->name('grammar.')->group(function () {
    $prefix = 'grammar';

    Route::controller(GrammarLevelController::class)
        ->prefix('levels')
        ->name('levels.')
        ->group(function () use ($prefix) {
            Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
            Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
            Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
            Route::get('/{grammar_level}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
            Route::put('/{grammar_level}', 'update')->name('update')->middleware("permission:$prefix.update");
            Route::delete('/{grammar_level}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
        });

    Route::controller(GrammarCategoryController::class)
        ->prefix('categories')
        ->name('categories.')
        ->group(function () use ($prefix) {
            Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
            Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
            Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
            Route::get('/{grammar_category}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
            Route::put('/{grammar_category}', 'update')->name('update')->middleware("permission:$prefix.update");
            Route::delete('/{grammar_category}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
        });

    Route::controller(GrammarPatternController::class)
        ->prefix('patterns')
        ->name('patterns.')
        ->group(function () use ($prefix) {
            Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
            Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
            Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
            Route::get('/{grammar_pattern}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
            Route::put('/{grammar_pattern}', 'update')->name('update')->middleware("permission:$prefix.update");
            Route::delete('/{grammar_pattern}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
        });
});
