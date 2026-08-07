<?php

declare(strict_types=1);

use App\Models\AuditLog;
use App\Services\Menu\MenuCache;
use App\Services\System\BackupService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('menu:clear', function (MenuCache $cache) {
    $cache->flush();
    $this->info('Menu cache flushed.');
})->purpose('Invalidate every cached navigation tree');

Artisan::command('backup:run', function (BackupService $backups) {
    $this->info('Backup created: '.$backups->create());
})->purpose('Create a database backup');

/*
|--------------------------------------------------------------------------
| Schedule (run by the `scheduler` container)
|--------------------------------------------------------------------------
*/

Schedule::command('backup:run')->dailyAt('02:00')->withoutOverlapping();

Schedule::call(function () {
    AuditLog::where('created_at', '<', now()->subDays((int) config('admin.audit.prune_days', 180)))->delete();
})->weekly()->name('prune-audit-logs');

Schedule::command('auth:clear-resets')->everyFifteenMinutes();
