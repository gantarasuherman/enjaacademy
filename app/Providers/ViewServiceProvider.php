<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Menu\MenuBuilder;
use App\Services\Setting\SettingService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Every layout gets its navigation straight from the database, never
        // from a hardcoded array. `MenuBuilder` caches per role signature.
        View::composer(['layouts.*', 'components.navigation.*'], function ($view) {
            $builder = app(MenuBuilder::class);

            $view->with([
                'sidebarMenus' => $builder->for('sidebar'),
                'topbarMenus' => $builder->for('topbar'),
                'footerMenus' => $builder->for('footer'),
            ]);
        });

        View::share('appSettings', fn () => app(SettingService::class)->all());

        // @canany-style helpers that stay quiet when the permission does not
        // exist yet (permissions are user-created data, after all).
        Blade::if('permission', function (?string $permission) {
            return $permission === null || auth()->check() && auth()->user()->can($permission);
        });

        Blade::if('anyPermission', function (string ...$permissions) {
            return auth()->check() && auth()->user()->canAny($permissions);
        });
    }
}
