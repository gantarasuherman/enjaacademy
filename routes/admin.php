<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagementAdminController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->name('home');

// Panduan penggunaan aplikasi — tersedia untuk semua admin yang login
// (tanpa permission khusus, ini hanya halaman bantuan statis).
Route::view('/panduan', 'admin.panduan')->name('panduan');

// Alur navigasi langkah demi langkah. Sama seperti panduan: statis, tanpa
// permission khusus — tiap langkah tetap dibatasi izinnya masing-masing saat
// benar-benar dibuka.
Route::view('/alur', 'admin.alur')->name('alur');

// ====================== MANAGEMENT ADMIN ======================
$prefix = 'management-admin';
Route::controller(ManagementAdminController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')
            ->name('index')
            ->middleware("permission:$prefix.index");

        Route::post('/update-general', 'updateGeneral')
            ->name('update-general')
            ->middleware("permission:$prefix.update");

        Route::post('/update-meta', 'updateMeta')
            ->name('update-meta')
            ->middleware("permission:$prefix.update");
    });

// ====================== MENU MANAGEMENT ======================
$prefix = 'menus';
Route::controller(MenuController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::get('/matrix', 'matrix')->name('matrix')->middleware("permission:$prefix.view");
        Route::put('/matrix', 'updateMatrix')->name('update-matrix')->middleware("permission:$prefix.update");
        Route::post('/clear-cache', 'clearCache')->name('clear-cache')->middleware("permission:$prefix.update");
        Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
        Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
        Route::post('/reorder', 'reorder')->name('reorder')->middleware("permission:$prefix.update");
        Route::post('/{menu}/duplicate', 'duplicate')->name('duplicate')->middleware("permission:$prefix.create");
        Route::get('/{menu}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
        Route::put('/{menu}', 'update')->name('update')->middleware("permission:$prefix.update");
        Route::delete('/{menu}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
    });

// ====================== ROLES ======================
$prefix = 'roles';
Route::controller(RoleController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::get('/matrix', 'matrix')->name('matrix')->middleware("permission:$prefix.view");
        Route::put('/matrix', 'updateMatrix')->name('update-matrix')->middleware("permission:$prefix.update");
        Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
        Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
        Route::get('/{role}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
        Route::put('/{role}', 'update')->name('update')->middleware("permission:$prefix.update");
        Route::delete('/{role}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
        Route::get('/{role}/permissions', 'permissions')->name('permissions')->middleware("permission:$prefix.update");
        Route::put('/{role}/permissions', 'updatePermissions')->name('update-permissions')->middleware("permission:$prefix.update");
    });

// ====================== PERMISSIONS ======================
$prefix = 'permissions';
Route::controller(PermissionController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
        Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
        Route::post('/generate', 'generate')->name('generate')->middleware("permission:$prefix.create");
        Route::get('/{permission}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
        Route::put('/{permission}', 'update')->name('update')->middleware("permission:$prefix.update");
        Route::delete('/{permission}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
    });

// ====================== USERS ======================
$prefix = 'users';
Route::controller(UserController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::get('/export', 'export')->name('export')->middleware("permission:$prefix.view");
        Route::get('/create', 'create')->name('create')->middleware("permission:$prefix.create");
        Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
        Route::get('/{user}', 'show')->name('show')->middleware("permission:$prefix.view");
        Route::get('/{user}/edit', 'edit')->name('edit')->middleware("permission:$prefix.update");
        Route::put('/{user}', 'update')->name('update')->middleware("permission:$prefix.update");
        Route::post('/{user}/toggle', 'toggle')->name('toggle')->middleware("permission:$prefix.update");
        Route::put('/{user}/roles', 'assignRoles')->name('assign-roles')->middleware("permission:$prefix.update");
        Route::delete('/{user}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
    });
