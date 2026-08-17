<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\LessonData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LessonRequest;
use App\Models\Lesson;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use App\Repositories\Contracts\LessonRepositoryInterface;
use App\Services\Learning\LearningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * A decluttered CRUD surface over the same `Lesson`/`LessonItem` models
 * `LessonController` manages — scoped to modules with `content_type=video`
 * only, with a form that drops every field irrelevant to video content
 * (rich-text editor, audio upload, the vocab/grammar item-type drawer) in
 * favour of a video URL and a simple ordered chapter list. Authorization
 * intentionally reuses the `Lesson` policy/`lessons.*` permissions — this is
 * the same resource, just a different lens on it, not a separate one.
 */
class VideoLessonController extends Controller
{
    public function __construct(
        private readonly LearningService $service,
        private readonly LessonRepositoryInterface $lessons,
        private readonly LearningModuleRepositoryInterface $modules,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lesson::class);

        return view('admin.video-lessons.index', [
            'lessons' => $this->lessons->paginate(
                [...$request->only(['search', 'module', 'is_published', 'sort', 'direction']), 'module_content_type' => 'video'],
                $this->perPage(),
            ),
            'modules' => $this->modules->forSelect('video'),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Lesson::class);

        return view('admin.video-lessons.form', [
            'lesson' => new Lesson([
                'learning_module_id' => $request->integer('module') ?: null,
                'estimated_minutes' => 10,
                'xp_reward' => 20,
            ]),
            'modules' => $this->modules->forSelect('video'),
            'chapters' => collect(),
        ]);
    }

    public function store(LessonRequest $request): RedirectResponse
    {
        $lesson = $this->service->createLesson(LessonData::fromRequest($request));

        return redirect()
            ->route('admin.video-lessons.edit', $lesson)
            ->with('success', __('Materi video ":title" berhasil dibuat.', ['title' => $lesson->title]));
    }

    public function edit(Lesson $lesson): View
    {
        $this->authorize('update', $lesson);

        return view('admin.video-lessons.form', [
            'lesson' => $lesson,
            'modules' => $this->modules->forSelect('video'),
            'chapters' => $lesson->items,
        ]);
    }

    public function update(LessonRequest $request, Lesson $lesson): RedirectResponse
    {
        $this->service->updateLesson($lesson, LessonData::fromRequest($request));

        return back()->with('success', __('Materi video ":title" berhasil diperbarui.', ['title' => $lesson->title]));
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        $this->authorize('delete', $lesson);

        $this->service->deleteLesson($lesson);

        return redirect()
            ->route('admin.video-lessons.index')
            ->with('success', __('Materi video ":title" berhasil dihapus.', ['title' => $lesson->title]));
    }
}
