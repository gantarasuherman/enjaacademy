<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\TrackUserActivity::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'admin' => \App\Http\Middleware\EnsureUserCanAccessAdmin::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('You do not have the required permission for this action.'),
                ], 403);
            }

            return response()->view('errors.403', [
                'message' => __('You do not have the required permission for this action.'),
            ], 403);
        });
    })->create();
