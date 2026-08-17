<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
use App\Http\Resources\ModuleResource;
use App\Models\Lesson;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use App\Repositories\Contracts\LessonRepositoryInterface;
use App\Services\Gamification\ProgressService;
use App\Services\Payment\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LearningController extends Controller
{
    public function __construct(
        private readonly LearningModuleRepositoryInterface $modules,
        private readonly LessonRepositoryInterface $lessons,
        private readonly ProgressService $progress,
        private readonly EnrollmentRepositoryInterface $enrollments,
        private readonly CheckoutService $checkout,
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

    /**
     * Enrolls the user if not already enrolled, otherwise unenrolls them.
     * This only records that the student took the class — it does not gate
     * access, which is still `study`/`{permission_prefix}.view`. The ENROLL
     * direction additionally requires paid access for paid modules — use
     * `OrderController::checkout()` first for those (unenrolling is always
     * allowed, no payment involved).
     */
    public function toggleEnrollment(Request $request, string $moduleSlug): JsonResponse
    {
        $module = $this->modules->findBySlug($moduleSlug);

        abort_if($module === null, 404);
        $this->authorize('study', $module);

        $alreadyEnrolled = $this->enrollments->isEnrolled($request->user(), $module);

        if (! $alreadyEnrolled && ! $this->checkout->hasPaidAccess($request->user(), $module)) {
            abort(402, __('This course requires payment — checkout first.'));
        }

        $enrolled = $this->enrollments->toggle($request->user(), $module);

        return response()->json([
            'data' => ['module' => $module->slug, 'enrolled' => $enrolled],
        ]);
    }

    /** Switches which enrolled course drives the student's Daily Quiz. */
    public function setActiveModule(Request $request, string $moduleSlug): JsonResponse
    {
        $module = $this->modules->findBySlug($moduleSlug);

        abort_if($module === null, 404);
        $this->authorize('study', $module);
        abort_unless($this->enrollments->isEnrolled($request->user(), $module), 422, __('You must take this course before making it active.'));

        $request->user()->update(['active_module_id' => $module->id]);

        return response()->json([
            'data' => ['module' => $module->slug, 'activeModuleId' => (string) $module->id],
        ]);
    }
}
