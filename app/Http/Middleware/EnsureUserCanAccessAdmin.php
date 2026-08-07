<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Coarse gate for the whole admin area. Individual routes still declare
 * `permission:{module}.{action}` — this only keeps students out of the shell.
 */
class EnsureUserCanAccessAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_if($user === null, 401);
        abort_unless($user->is_active, 403, __('Your account is inactive.'));
        abort_unless($user->canAccessAdminPanel(), 403, __('You do not have access to the admin panel.'));

        return $next($request);
    }
}
