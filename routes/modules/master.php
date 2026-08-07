<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LearningModuleController;
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
