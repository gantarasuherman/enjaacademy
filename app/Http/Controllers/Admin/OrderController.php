<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningModule;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Read-only — orders are financial records, not editable from the admin UI.
 */
class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        return view('admin.orders.index', [
            'orders' => Order::query()
                ->with(['user:id,name,email', 'learningModule:id,name,slug'])
                ->when($request->filled('search'), fn (Builder $q) => $q->whereHas(
                    'user',
                    fn (Builder $u) => $u->where('name', 'like', '%'.$request->string('search').'%')
                        ->orWhere('email', 'like', '%'.$request->string('search').'%'),
                ))
                ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')))
                ->when($request->filled('module'), fn (Builder $q) => $q->where('learning_module_id', $request->integer('module')))
                ->latest()
                ->paginate($this->perPage())
                ->withQueryString(),
            'modules' => LearningModule::query()->where('is_paid', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
