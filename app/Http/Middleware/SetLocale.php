<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('app.supported_locales', ['id' => 'Indonesia', 'en' => 'English']));

        $locale = $request->session()->get('locale')
            ?? $request->user()?->locale
            ?? config('app.locale');

        if (in_array($locale, $supported, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
