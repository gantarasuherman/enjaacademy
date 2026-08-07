<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
use App\Http\Resources\ModuleResource;
use App\Models\Lesson;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use App\Repositories\Contracts\LessonRepositoryInterface;
use App\Services\Gamification\ProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LearningController extends Controller
{
    public function __construct(
        private readonly LearningModuleRepositoryInterface $modules,
        private readonly LessonRepositoryInterface $lessons,
        private readonly ProgressService $progress,
    ) {}

    public function modules(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->modules->accessibleFor($request->user())
                ->map(fn ($group) => ModuleResource::collection($group)),
        ]);
    }

    public function lessons(Request $request, string $moduleSlug): AnonymousResourceCollection
    {
        $module = $this->modules->findBySlug($moduleSlug);

        abort_if($module === null, 404);
        $this->authorize('study', $module);

        return LessonResource::collection($this->lessons->publishedForModule($module->id));
    }

    public function lesson(Request $request, Lesson $lesson): LessonResource
    {
        $this->authorize('view', $lesson);

        $this->progress->trackLessonView($request->user(), $lesson);

        return new LessonResource($lesson->load(['module', 'items' => fn ($q) => $q->where('is_active', true)]));
    }

    public function complete(Request $request, Lesson $lesson): JsonResponse
    {
        $this->authorize('view', $lesson);

        $levels = $this->progress->completeLesson($request->user(), $lesson);

        return response()->json([
            'message' => __('Lesson completed.'),
            'levels_gained' => $levels,
            'stat' => $request->user()->stat()->first(),
        ]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        return response()->json($this->progress->dashboardFor($request->user()));
    }
}
