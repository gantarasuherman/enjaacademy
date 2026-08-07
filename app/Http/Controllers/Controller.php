<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /** Page size for admin listings, overridable per request. */
    protected function perPage(): int
    {
        $requested = (int) request()->integer('per_page');

        return in_array($requested, [10, 15, 25, 50, 100], true)
            ? $requested
            : (int) config('admin.per_page', 15);
    }
}
