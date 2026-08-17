<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LearningModuleController;
use App\Http\Controllers\Admin\VocabularyWordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Master data
|--------------------------------------------------------------------------
*/

// ====================== LANGUAGES ======================
$prefix = 'languages';
Route::controller(LanguageController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
        Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
        Route::get('/{language}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
        Route::put('/{language}', 'update')->name('update')->middleware("permission:$prefix.update");
        Route::delete('/{language}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
    });

// ====================== LEARNING MODULES ======================
$prefix = 'modules';
Route::controller(LearningModuleController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
        Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
        Route::get('/{module}', 'show')->name('show')->middleware("permission:$prefix.view");
        Route::get('/{module}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
        Route::put('/{module}', 'update')->name('update')->middleware("permission:$prefix.update");
        Route::delete('/{module}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
    });

// ====================== ACHIEVEMENTS ======================
$prefix = 'achievements';
Route::controller(AchievementController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
        Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
        Route::get('/{achievement}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
        Route::put('/{achievement}', 'update')->name('update')->middleware("permission:$prefix.update");
        Route::delete('/{achievement}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
    });

// ====================== VOCABULARY BANK (Daily Quiz) ======================
$prefix = 'vocabulary-words';
Route::controller(VocabularyWordController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::get('/template', 'template')->name('template')->middleware("permission:$prefix.create");
        Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
        Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
        Route::post('/import', 'import')->name('import')->middleware("permission:$prefix.create");
        // No permission middleware here — the controller itself checks
        // create-OR-update (this is used from both "Tambah kata" and "Edit
        // kata"), the surrounding admin route group already requires auth.
        Route::post('/ai/generate', 'generateWithAi')->name('ai.generate');
        // Registered before the `{vocabulary_word}` routes below — a literal
        // segment must win over the wildcard, and Laravel matches in
        // registration order.
        Route::delete('/bulk-destroy', 'bulkDestroy')->name('bulk-destroy')->middleware("permission:$prefix.delete");
        Route::get('/{vocabulary_word}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
        Route::put('/{vocabulary_word}', 'update')->name('update')->middleware("permission:$prefix.update");
        Route::delete('/{vocabulary_word}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
    });
