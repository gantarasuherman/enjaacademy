<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Menu\MenuBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Serves the same permission-filtered navigation the Blade layouts use, so an
 * SPA or mobile client gets an identical menu without duplicating the rules.
 */
class MenuController extends Controller
{
    public function __construct(private readonly MenuBuilder $builder) {}

    public function index(Request $request, string $position = 'sidebar'): JsonResponse
    {
        abort_unless(in_array($position, array_keys(config('admin.menu.positions')), true), 404);

        return response()->json([
            'position' => $position,
            'data' => $this->builder->for($position, $request->user()),
        ]);
    }

    public function all(Request $request): JsonResponse
    {
        return response()->json([
            'sidebar' => $this->builder->for('sidebar', $request->user()),
            'topbar' => $this->builder->for('topbar', $request->user()),
            'footer' => $this->builder->for('footer', $request->user()),
        ]);
    }
}
