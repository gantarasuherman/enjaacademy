<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Lesson;
use App\Repositories\Contracts\ProgressRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(private readonly ProgressRepositoryInterface $progress) {}

    /**
     * One row per active module — `issuedAt` is null until the student
     * reaches 100% (see `ProgressService::maybeIssueCertificate()`), so the
     * frontend can show both earned and in-progress cards from a single call.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $modules = $this->progress->moduleCompletion($user);

        $issuedAt = Certificate::query()
            ->where('user_id', $user->id)
            ->pluck('issued_at', 'learning_module_id');

        // A module's own `lessons` relation isn't eager-loaded by
        // `moduleCompletion()` — batched separately to avoid both N+1 queries
        // and the app's `Model::shouldBeStrict()` lazy-loading guard outside production.
        $levelsByModule = Lesson::query()
            ->whereIn('learning_module_id', $modules->pluck('id'))
            ->whereNotNull('level')
            ->select('learning_module_id', 'level')
            ->distinct()
            ->get()
            ->groupBy('learning_module_id');

        $certificates = $modules->map(function ($module) use ($issuedAt, $levelsByModule) {
            $levels = $levelsByModule->get($module->id, collect());

            return [
                'id' => (string) $module->id,
                'title' => $module->name,
                'moduleId' => $module->slug,
                'cefr' => $levels->count() === 1 ? $levels->first()->level : null,
                'issuedAt' => $issuedAt->get($module->id)?->toIso8601String(),
                'requirement' => "Selesaikan seluruh {$module->lessons_count} materi modul ini.",
                'progressPercent' => $module->completion_percent,
            ];
        })->values();

        return response()->json(['data' => $certificates]);
    }
}
