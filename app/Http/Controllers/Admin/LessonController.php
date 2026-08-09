<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\LessonData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LessonRequest;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use App\Repositories\Contracts\LessonRepositoryInterface;
use App\Services\AI\LessonAiService;
use App\Services\Learning\LearningService;
use App\Services\System\ImportExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class LessonController extends Controller
{
    public function __construct(
        private readonly LearningService $service,
        private readonly LessonRepositoryInterface $lessons,
        private readonly LearningModuleRepositoryInterface $modules,
        private readonly ImportExportService $io,
        private readonly LessonAiService $ai,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lesson::class);

        return view('admin.lessons.index', [
            'lessons' => $this->lessons->paginate(
                $request->only(['search', 'module', 'level', 'is_published', 'sort', 'direction']),
                $this->perPage(),
            ),
            'modules' => $this->modules->forSelect(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Lesson::class);

        return view('admin.lessons.form', [
            'lesson' => new Lesson([
                'learning_module_id' => $request->integer('module') ?: null,
                'estimated_minutes' => 10,
                'xp_reward' => 20,
            ]),
            'modules' => $this->modules->forSelect(),
            'items' => collect(),
        ]);
    }

    public function store(LessonRequest $request): RedirectResponse
    {
        $lesson = $this->service->createLesson(LessonData::fromRequest($request));

        return redirect()
            ->route('admin.lessons.edit', $lesson)
            ->with('success', __('Lesson ":title" was created.', ['title' => $lesson->title]));
    }

    public function edit(Lesson $lesson): View
    {
        $this->authorize('update', $lesson);

        return view('admin.lessons.form', [
            'lesson' => $lesson,
            'modules' => $this->modules->forSelect(),
            'items' => $lesson->items,
        ]);
    }

    public function update(LessonRequest $request, Lesson $lesson): RedirectResponse
    {
        $this->service->updateLesson($lesson, LessonData::fromRequest($request));

        return back()->with('success', __('Lesson ":title" was updated.', ['title' => $lesson->title]));
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        $this->authorize('delete', $lesson);

        $this->service->deleteLesson($lesson);

        return redirect()
            ->route('admin.lessons.index')
            ->with('success', __('Lesson ":title" was deleted.', ['title' => $lesson->title]));
    }

    public function reorder(Request $request): JsonResponse
    {
        $this->authorize('update', Lesson::class);

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:lessons,id'],
        ]);

        $this->service->reorderLessons($validated['ids']);

        return response()->json(['message' => __('Lesson order saved.')]);
    }

    /* ------------------------------------------------------------------
     | Bulk item import / export
     | ------------------------------------------------------------------
     */

    public function import(Request $request, Lesson $lesson): RedirectResponse
    {
        $this->authorize('import', Lesson::class);

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:8192'],
        ]);

        $count = $this->io->importLessonItems($lesson, $request->file('file'));

        return back()->with('success', __(':count item(s) imported.', ['count' => $count]));
    }

    public function export(Lesson $lesson): StreamedResponse
    {
        $this->authorize('export', Lesson::class);

        return $this->io->streamCsv(
            "lesson-{$lesson->slug}-items.csv",
            ['term', 'reading', 'romaji', 'meaning', 'example', 'example_meaning'],
            $lesson->items->map(fn (LessonItem $item) => [
                $item->term, $item->reading, $item->romaji,
                $item->meaning, $item->example, $item->example_meaning,
            ]),
        );
    }

    public function template(): StreamedResponse
    {
        $this->authorize('import', Lesson::class);

        return $this->io->lessonItemTemplate();
    }

    /* ------------------------------------------------------------------
     | AI content generation (Gemini — degrades gracefully if no API key
     | is configured, see config/services.php:gemini and GEMINI_API_KEY)
     | ------------------------------------------------------------------
     */

    public function generateItems(Request $request): JsonResponse
    {
        $this->authorize('create', Lesson::class);

        if (! $this->ai->available()) {
            return response()->json([
                'available' => false,
                'message' => __('Fitur AI belum diaktifkan. Hubungi admin untuk mengatur API key.'),
            ]);
        }

        $validated = $request->validate([
            'topic' => ['required', 'string', 'max:200'],
            'level' => ['nullable', 'string', 'max:20'],
            'count' => ['nullable', 'integer', 'min:1', 'max:30'],
            'types' => ['nullable', 'array'],
            'language' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $items = $this->ai->generateItems($validated);

            return response()->json(['available' => true, 'items' => $items]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'available' => true,
                'error' => true,
                'message' => __('Gagal menghubungi layanan AI. Coba lagi sebentar lagi.'),
            ]);
        }
    }

    public function generateTranslation(Request $request): JsonResponse
    {
        $this->authorize('create', Lesson::class);

        if (! $this->ai->available()) {
            return response()->json([
                'available' => false,
                'message' => __('Fitur AI belum diaktifkan. Hubungi admin untuk mengatur API key.'),
            ]);
        }

        $validated = $request->validate([
            'content' => ['required', 'string'],
            'level' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $translation = $this->ai->generateTranslation($validated['content'], $validated['level'] ?? null);

            return response()->json(['available' => true, 'translation' => $translation]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'available' => true,
                'error' => true,
                'message' => __('Gagal menghubungi layanan AI. Coba lagi sebentar lagi.'),
            ]);
        }
    }
}
