<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly LearningModuleRepositoryInterface $modules,
        private readonly AuditLogRepositoryInterface $audits,
    ) {}

    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::count(),
                'active_users' => User::active()->count(),
                'lessons' => Lesson::count(),
                'published_lessons' => Lesson::published()->count(),
                'quizzes' => Quiz::count(),
                'attempts_today' => QuizAttempt::whereDate('created_at', today())->count(),
            ],
            'usersByRole' => $this->users->countByRole(),
            'registrations' => $this->users->registrationsPerMonth(12),
            'recentUsers' => $this->users->recentlyRegistered(6),
            'leaderboard' => $this->users->leaderboard(8),
            'modules' => $this->modules->withLessonCounts(),
            'recentActivity' => $this->audits->paginate([], 8),
        ]);
    }
}
