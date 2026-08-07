<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Gamification\ProgressService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps `last_activity_at` fresh and rolls the daily learning streak.
 * Throttled to one write per five minutes per user.
 */
class TrackUserActivity
{
    public function __construct(private readonly ProgressService $progress) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $request->isMethod('OPTIONS')) {
            $stale = $user->last_activity_at === null
                || $user->last_activity_at->lt(now()->subMinutes(5));

            if ($stale) {
                $user->forceFill(['last_activity_at' => now()])->saveQuietly();
                $this->progress->touchStreak($user);
            }
        }

        return $next($request);
    }
}
