<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Services\System\ImportExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    public function __construct(
        private readonly AuditLogRepositoryInterface $audits,
        private readonly ImportExportService $io,
    ) {}

    private array $filterKeys = ['search', 'event', 'user_id', 'auditable_type', 'from', 'to', 'sort', 'direction'];

    public function index(Request $request): View
    {
        return view('admin.audit-logs.index', [
            'logs' => $this->audits->paginate($request->only($this->filterKeys), $this->perPage()),
            'events' => $this->audits->events(),
            'types' => $this->audits->auditableTypes(),
            'users' => User::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function show(AuditLog $auditLog): View
    {
        return view('admin.audit-logs.show', [
            'log' => $auditLog->load('user'),
            'changes' => $auditLog->changes(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $logs = $this->audits
            ->paginate($request->only($this->filterKeys), 10000)
            ->getCollection();

        return $this->io->streamCsv(
            'audit-logs-'.now()->format('Ymd-His').'.csv',
            ['id', 'when', 'user', 'event', 'subject', 'label', 'ip', 'description'],
            $logs->map(fn (AuditLog $log) => [
                $log->id,
                $log->created_at?->toDateTimeString(),
                $log->user_name,
                $log->event,
                $log->auditable_type,
                $log->auditable_label,
                $log->ip_address,
                $log->description,
            ]),
        );
    }

    /** Housekeeping: drop entries older than the configured retention. */
    public function prune(Request $request): RedirectResponse
    {
        $days = (int) $request->validate([
            'days' => ['nullable', 'integer', 'min:1', 'max:3650'],
        ])['days'] ?: (int) config('admin.audit.prune_days', 180);

        $deleted = $this->audits->prune($days);

        return back()->with('success', __(':count log entries older than :days days were removed.', [
            'count' => $deleted,
            'days' => $days,
        ]));
    }
}
