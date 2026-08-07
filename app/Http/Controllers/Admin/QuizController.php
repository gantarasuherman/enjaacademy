<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\QuizData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuizRequest;
use App\Models\Quiz;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use App\Repositories\Contracts\LessonRepositoryInterface;
use App\Repositories\Contracts\QuizRepositoryInterface;
use App\Services\Learning\QuizService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function __construct(
        private readonly QuizService $service,
        private readonly QuizRepositoryInterface $quizzes,
        private readonly LearningModuleRepositoryInterface $modules,
        private readonly LessonRepositoryInterface $lessons,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Quiz::class);

        return view('admin.quizzes.index', [
            'quizzes' => $this->quizzes->paginate(
                $request->only(['search', 'module', 'difficulty', 'is_published', 'sort', 'direction']),
                $this->perPage(),
            ),
            'modules' => $this->modules->forSelect(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Quiz::class);

        return view('admin.quizzes.form', [
            'quiz' => new Quiz([
                'difficulty' => 'easy',
                'pass_score' => 70,
                'xp_reward' => 50,
                'shuffle_questions' => true,
                'shuffle_options' => true,
                'show_explanation' => true,
            ]),
            'modules' => $this->modules->forSelect(),
            'lessons' => $this->lessons->forSelect(),
            'questions' => collect(),
        ]);
    }

    public function store(QuizRequest $request): RedirectResponse
    {
        $quiz = $this->service->create(QuizData::fromRequest($request));

        return redirect()
            ->route('admin.quizzes.edit', $quiz)
            ->with('success', __('Quiz ":title" was created.', ['title' => $quiz->title]));
    }

    public function show(Quiz $quiz): View
    {
        $this->authorize('view', $quiz);

        return view('admin.quizzes.show', [
            'quiz' => $quiz->load('questions.options', 'module'),
            'attempts' => $quiz->attempts()->with('user:id,name')->latest()->limit(20)->get(),
            'stats' => [
                'attempts' => $quiz->attempts()->count(),
                'average' => (int) round((float) $quiz->attempts()->finished()->avg('score')),
                'pass_rate' => (int) round(
                    $quiz->attempts()->finished()->count() > 0
                        ? $quiz->attempts()->where('passed', true)->count() / $quiz->attempts()->finished()->count() * 100
                        : 0
                ),
            ],
        ]);
    }

    public function edit(Quiz $quiz): View
    {
        $this->authorize('update', $quiz);

        return view('admin.quizzes.form', [
            'quiz' => $quiz,
            'modules' => $this->modules->forSelect(),
            'lessons' => $this->lessons->forSelect(),
            'questions' => $quiz->questions()->with('options')->get(),
        ]);
    }

    public function update(QuizRequest $request, Quiz $quiz): RedirectResponse
    {
        $this->service->update($quiz, QuizData::fromRequest($request));

        return back()->with('success', __('Quiz ":title" was updated.', ['title' => $quiz->title]));
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        $this->authorize('delete', $quiz);

        $this->service->delete($quiz);

        return redirect()
            ->route('admin.quizzes.index')
            ->with('success', __('Quiz ":title" was deleted.', ['title' => $quiz->title]));
    }
}
