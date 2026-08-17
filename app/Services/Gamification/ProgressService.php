<?php

declare(strict_types=1);

namespace App\Services\Gamification;

use App\Models\Certificate;
use App\Models\LearningModule;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserStat;
use App\Models\XpLog;
use App\Repositories\Contracts\ProgressRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Owns XP, levels, streaks and the denormalised counters on `user_stats`.
 */
class ProgressService
{
    public function __construct(
        private readonly ProgressRepositoryInterface $progress,
        private readonly AchievementService $achievements,
    ) {}

    /**
     * Award XP and recalculate the level. Returns the levels gained, so the
     * caller can show a "level up!" toast.
     */
    public function awardXp(User $user, int $amount, string $reason, ?Model $source = null): int
    {
        if ($amount === 0) {
            return 0;
        }

        return DB::transaction(function () use ($user, $amount, $reason, $source) {
            $stat = $this->progress->statFor($user);

            XpLog::create([
                'user_id' => $user->id,
                'source_type' => $source?->getMorphClass(),
                'source_id' => $source?->getKey(),
                'amount' => $amount,
                'reason' => $reason,
            ]);

            $before = $stat->level;

            $stat->xp_total = max(0, $stat->xp_total + $amount);
            $stat->level = UserStat::levelForXp((int) $stat->xp_total);
            $stat->save();

            $this->achievements->evaluate($user, $stat);

            return max(0, $stat->level - $before);
        });
    }

    /** Called on every authenticated request via TrackUserActivity. */
    public function touchStreak(User $user): void
    {
        $stat = $this->progress->statFor($user);
        $today = now()->toDateString();

        if ($stat->last_active_date?->toDateString() === $today) {
            return;
        }

        $wasYesterday = $stat->last_active_date?->toDateString() === now()->subDay()->toDateString();

        $stat->streak_days = $wasYesterday ? $stat->streak_days + 1 : 1;
        $stat->longest_streak = max($stat->longest_streak, $stat->streak_days);
        $stat->last_active_date = $today;
        $stat->save();

        if ($wasYesterday) {
            $this->awardXp($user, (int) config('admin.gamification.xp.daily_streak'), __('Daily streak'), null);
        }

        $this->achievements->evaluate($user, $stat->refresh());
    }

    public function completeLesson(User $user, Lesson $lesson): int
    {
        $existing = $user->progress()
            ->where('trackable_type', $lesson->getMorphClass())
            ->where('trackable_id', $lesson->id)
            ->first();

        $alreadyDone = $existing?->status === 'completed';

        $this->progress->touch($user, $lesson, 100, 'completed');

        if ($alreadyDone) {
            return 0; // XP is awarded once per lesson
        }

        $stat = $this->progress->statFor($user);
        $stat->increment('lessons_completed');
        $stat->increment('minutes_learned', (int) $lesson->estimated_minutes);

        $this->syncModuleProgress($user, $lesson);

        return $this->awardXp(
            $user,
            (int) ($lesson->xp_reward ?: config('admin.gamification.xp.lesson_completed')),
            __('Completed lesson: :title', ['title' => $lesson->title]),
            $lesson,
        );
    }

    public function trackLessonView(User $user, Lesson $lesson, int $percent = 25): void
    {
        $this->progress->touch($user, $lesson, $percent);
    }

    /**
     * One-time XP for a skill-practice attempt (Speaking exercise, Writing
     * prompt, …) against any trackable model. Marks the attempt as visited
     * either way, but XP is only ever granted the first time THIS trackable
     * ever produces a non-zero amount for this user — checked against
     * `XpLog` directly (not `UserProgress.status`) so a first attempt that
     * scores too low for XP doesn't block a later, better attempt from
     * still earning it, while a retry after already earning XP can't farm
     * more by resubmitting.
     */
    public function completeSkillPractice(User $user, Model $trackable, int $xp, string $reason): int
    {
        $this->progress->touch($user, $trackable, 100, 'completed');

        if ($xp <= 0) {
            return 0;
        }

        $alreadyAwarded = XpLog::where('user_id', $user->id)
            ->where('source_type', $trackable->getMorphClass())
            ->where('source_id', $trackable->getKey())
            ->exists();

        if ($alreadyAwarded) {
            return 0;
        }

        // Not `return $this->awardXp(...)` — that call returns levels
        // gained (for a "level up!" toast elsewhere), not the XP amount,
        // which is what the caller here actually needs to report back.
        $this->awardXp($user, $xp, $reason, $trackable);

        return $xp;
    }

    /** Grants quiz XP and updates the quiz counters. */
    public function recordQuizAttempt(User $user, QuizAttempt $attempt, Quiz $quiz): int
    {
        $stat = $this->progress->statFor($user);
        $stat->increment('quizzes_completed');

        if ($attempt->isPerfect()) {
            $stat->increment('perfect_quizzes');
        }

        $xp = 0;

        if ($attempt->passed) {
            $xp = (int) ($quiz->xp_reward ?: config('admin.gamification.xp.quiz_passed'));

            if ($attempt->isPerfect()) {
                $xp += (int) config('admin.gamification.xp.quiz_perfect_bonus');
            }
        }

        $attempt->update(['earned_xp' => $xp]);

        return $xp > 0
            ? $this->awardXp($user, $xp, __('Quiz: :title', ['title' => $quiz->title]), $quiz)
            : 0;
    }

    public function recordFlashcardReview(User $user, int $count = 1): void
    {
        $stat = $this->progress->statFor($user);
        $stat->increment('flashcards_reviewed', $count);

        $this->awardXp(
            $user,
            $count * (int) config('admin.gamification.xp.flashcard_reviewed'),
            __('Flashcard review'),
        );
    }

    /** Roll the lesson completion ratio up onto the parent module. */
    private function syncModuleProgress(User $user, Lesson $lesson): void
    {
        $module = $lesson->module;

        if (! $module) {
            return;
        }

        $total = $module->lessons()->where('is_published', true)->count();

        if ($total === 0) {
            return;
        }

        $done = $user->progress()
            ->where('trackable_type', 'lesson')
            ->whereIn('trackable_id', $module->lessons()->pluck('id'))
            ->where('status', 'completed')
            ->count();

        $this->progress->touch($user, $module, (int) round($done / $total * 100));

        if ($done === $total) {
            $this->maybeIssueCertificate($user, $module);
        }
    }

    /** Idempotent: a module can only ever earn one certificate per user. */
    private function maybeIssueCertificate(User $user, LearningModule $module): void
    {
        Certificate::firstOrCreate(
            ['user_id' => $user->id, 'learning_module_id' => $module->id],
            ['issued_at' => now()],
        );
    }

    /** Everything the student dashboard needs, in one call. */
    public function dashboardFor(User $user): array
    {
        $stat = $this->progress->statFor($user);

        return [
            'stat' => $stat,
            'level' => $stat->level,
            'xp_total' => $stat->xp_total,
            'xp_into_level' => $stat->xpIntoCurrentLevel(),
            'xp_for_next' => $stat->xpNeededForNextLevel(),
            'level_percent' => $stat->levelProgressPercent(),
            'xp_per_day' => $this->progress->xpPerDay($user, 30),
            'modules' => $this->progress->moduleCompletion($user),
            'recent' => $this->progress->recentActivity($user, 8),
            'active_module_id' => $user->active_module_id ? (string) $user->active_module_id : null,
        ];
    }
}
