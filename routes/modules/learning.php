<?php

declare(strict_types=1);

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
