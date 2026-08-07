<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Serves the React learner app.
 *
 * The SPA owns every learner-facing screen; Laravel keeps the admin panel
 * (Blade) and the API. The named routes below still exist so the dynamic menu
 * can point at them — that is why the seeded sidebar keeps working.
 */
class SpaController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        // In development the SPA runs on its own Vite server; there is no
        // build to serve, so forward there — carrying the path across so a
        // deep link still lands on the right screen.
        if (! file_exists(public_path('app/index.html'))) {
            $base = rtrim((string) config('admin.spa_dev_url'), '/');

            return redirect()->away($base.'/'.ltrim($request->path(), '/'));
        }

        return view('spa');
    }
}
