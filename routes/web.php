<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';
require __DIR__.'/public.php';

Route::middleware(['auth', 'verified'])->group(function () {
    require __DIR__.'/peserta.php';
});

$adminPrefix = config('admin.prefix', 'admin');

Route::prefix($adminPrefix)
    ->middleware(['auth', 'verified', 'admin'])
    ->name('admin.')
    ->group(function () {
        require __DIR__.'/admin.php';
        require __DIR__.'/modules/master.php';
        require __DIR__.'/modules/learning.php';
        require __DIR__.'/modules/report.php';
        require __DIR__.'/modules/system.php';
    });
