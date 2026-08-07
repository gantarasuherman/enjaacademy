<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\System\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    public function __construct(private readonly BackupService $backups) {}

    public function index(): View
    {
        return view('admin.backups.index', [
            'backups' => $this->backups->list(),
            'keep' => config('admin.backup.keep'),
        ]);
    }

    public function store(): RedirectResponse
    {
        try {
            $file = $this->backups->create();
        } catch (ProcessFailedException $e) {
            return back()->with('error', __('Backup failed: :message', [
                'message' => trim($e->getProcess()->getErrorOutput()) ?: $e->getMessage(),
            ]));
        }

        return back()->with('success', __('Backup :file created.', ['file' => $file]));
    }

    public function download(string $filename): BinaryFileResponse
    {
        return response()->download($this->backups->download($filename));
    }

    public function destroy(string $filename): RedirectResponse
    {
        $this->backups->delete($filename);

        return back()->with('success', __('Backup :file deleted.', ['file' => $filename]));
    }
}
