<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningModule;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserProgress;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\System\ImportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly ImportExportService $io,
    ) {}

    /** Learner progress across modules. */
    public function progress(Request $request): View
    {
        $moduleId = $request->integer('module') ?: null;

        $rows = User::query()
            ->active()
            ->with('stat')
            ->when($request->string('search')->toString(), fn ($q, $s) => $q->search($s))
            ->withCount([
                'progress as completed_lessons' => fn ($q) => $q
                    ->where('trackable_type', 'lesson')
                    ->where('status', 'completed'),
            ])
            ->orderByDesc('completed_lessons')
            ->paginate($this->perPage())
            ->withQueryString();

        return view('admin.reports.progress', [
            'rows' => $rows,
            'modules' => LearningModule::orderBy('name')->pluck('name', 'id'),
            'moduleId' => $moduleId,
            'totals' => [
                'learners' => User::active()->count(),
                'completions' => UserProgress::completed()->count(),
                'in_progress' => UserProgress::where('status', 'in_progress')->count(),
            ],
        ]);
    }

    /** Quiz performance per quiz. */
    public function quiz(Request $request): View
    {
        $rows = Quiz::query()
            ->withCount('attempts')
            ->withAvg(['attempts as average_score' => fn ($q) => $q->whereNotNull('finished_at')], 'score')
            ->with('module:id,name')
            ->when($request->string('search')->toString(), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->orderByDesc('attempts_count')
            ->paginate($this->perPage())
            ->withQueryString();

        return view('admin.reports.quiz', [
            'rows' => $rows,
            'daily' => QuizAttempt::query()
                ->where('created_at', '>=', now()->subDays(29)->startOfDay())
                ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->groupBy('day')
                ->pluck('total', 'day'),
        ]);
    }

    /** XP leaderboard. */
    public function leaderboard(): View
    {
        return view('admin.reports.leaderboard', [
            'leaders' => $this->users->leaderboard(50),
        ]);
    }

    /** Registration + engagement summary. */
    public function activity(): View
    {
        return view('admin.reports.activity', [
            'registrations' => $this->users->registrationsPerMonth(12),
            'byRole' => $this->users->countByRole(),
            'activeToday' => User::whereDate('last_activity_at', today())->count(),
            'activeWeek' => User::where('last_activity_at', '>=', now()->subWeek())->count(),
            'topModules' => DB::table('user_progress')
                ->join('lessons', function ($join) {
                    $join->on('lessons.id', '=', 'user_progress.trackable_id')
                        ->where('user_progress.trackable_type', '=', 'lesson');
                })
                ->join('learning_modules', 'learning_modules.id', '=', 'lessons.learning_module_id')
                ->select('learning_modules.name', DB::raw('COUNT(*) as total'))
                ->groupBy('learning_modules.id', 'learning_modules.name')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = User::query()
            ->active()
            ->with('stat')
            ->withCount([
                'progress as completed_lessons' => fn ($q) => $q
                    ->where('trackable_type', 'lesson')
                    ->where('status', 'completed'),
            ])
            ->orderByDesc('completed_lessons')
            ->limit(10000)
            ->get();

        return $this->io->streamCsv(
            'progress-report-'.now()->format('Ymd-His').'.csv',
            ['user', 'email', 'level', 'xp', 'streak', 'lessons_completed', 'quizzes_completed'],
            $rows->map(fn (User $user) => [
                $user->name,
                $user->email,
                $user->stat?->level ?? 1,
                $user->stat?->xp_total ?? 0,
                $user->stat?->streak_days ?? 0,
                $user->completed_lessons,
                $user->stat?->quizzes_completed ?? 0,
            ]),
        );
    }
}
