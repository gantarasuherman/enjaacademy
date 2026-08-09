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
        // The admin panel (Blade) authenticates via the "web" session guard;
        // the SPA authenticates separately via a Sanctum bearer token in
        // localStorage. Without this, a staff member clicking "Belajar" in
        // the sidebar would hit the SPA's own login screen despite already
        // being signed in. Bridge the two: mint a token for the web-session
        // user and hand it to the SPA once, via a query param it consumes on
        // boot (see frontend/src/main.tsx) and strips from the address bar.
        //
        // Guarded by `!$request->has('sso')` so the redirect below only
        // fires once — the second pass (now carrying `sso`) falls through to
        // the normal dev-proxy/view branches with the param intact.
        if (! $request->has('sso') && auth()->check()) {
            return redirect()->to($request->fullUrlWithQuery(['sso' => $this->mintSsoToken()]));
        }

        // In development the SPA runs on its own Vite server; there is no
        // build to serve, so forward there — carrying the path (and any
        // `sso` query param) across so a deep link still lands on the right
        // screen, already authenticated.
        if (! file_exists(public_path('app/index.html'))) {
            $base = rtrim((string) config('admin.spa_dev_url'), '/');
            $query = $request->getQueryString();

            return redirect()->away($base.'/'.ltrim($request->path(), '/').($query ? "?{$query}" : ''));
        }

        return view('spa');
    }

    /**
     * Same ability-scoping rule as `AuthController::login()` — a superadmin
     * gets `['*']` (full access), everyone else gets their real permission
     * set, so the SPA ends up under exactly the same RBAC as the web panel.
     * Old bridge tokens for this user are cleared first so they don't pile
     * up on every "Belajar" click.
     */
    private function mintSsoToken(): string
    {
        $user = auth()->user();
        $user->tokens()->where('name', 'spa-sso')->delete();

        $abilities = $user->isSuperAdmin() ? ['*'] : $user->getAllPermissions()->pluck('name')->all();

        return $user->createToken('spa-sso', $abilities ?: ['basic'])->plainTextToken;
    }
}
