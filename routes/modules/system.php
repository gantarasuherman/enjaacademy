<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BackupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| System
|--------------------------------------------------------------------------
*/

// ====================== AUDIT LOG ======================
$prefix = 'audit-logs';
Route::controller(AuditLogController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::get('/export', 'export')->name('export')->middleware("permission:$prefix.view");
        Route::delete('/prune', 'prune')->name('prune')->middleware("permission:$prefix.delete");
        Route::get('/{auditLog}', 'show')->name('show')->middleware("permission:$prefix.view");
    });

// ====================== BACKUP ======================
$prefix = 'backups';
Route::controller(BackupController::class)
    ->prefix($prefix)
    ->name("$prefix.")
    ->group(function () use ($prefix) {
        Route::get('/', 'index')->name('index')->middleware("permission:$prefix.view");
        Route::post('/', 'store')->name('store')->middleware("permission:$prefix.create");
        Route::get('/{filename}/download', 'download')->name('download')->middleware("permission:$prefix.view");
        Route::delete('/{filename}', 'destroy')->name('destroy')->middleware("permission:$prefix.delete");
    });
