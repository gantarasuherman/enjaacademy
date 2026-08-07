<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\LearningModule;
use App\Models\User;
use App\Models\UserProgress;
use App\Models\UserStat;
use App\Models\XpLog;
use App\Repositories\Contracts\ProgressRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProgressRepository extends BaseRepository implements ProgressRepositoryInterface
{
    protected string $defaultSort = 'last_activity_at';

    protected array $sortable = ['id', 'progress_percent', 'last_activity_at', 'completed_at'];

    public function model(): string
    {
        return UserProgress::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['user_id'] ?? null, fn (Builder $q, $id) => $q->where('user_id', $id))
            ->when($filters['status'] ?? null, fn (Builder $q, $s) => $q->where('status', $s))
            ->when($filters['trackable_type'] ?? null, fn (Builder $q, $t) => $q->where('trackable_type', $t));
    }

    public function statFor(User $user): UserStat
    {
        return UserStat::firstOrCreate(['user_id' => $user->id]);
    }

    public function touch(User $user, Model $trackable, int $percent, ?string $status = null): UserProgress
    {
        $percent = max(0, min(100, $percent));
        $status ??= $percent >= 100 ? 'completed' : 'in_progress';

        /** @var UserProgress $progress */
        $progress = UserProgress::firstOrNew([
            'user_id' => $user->id,
            'trackable_type' => $trackable->getMorphClass(),
            'trackable_id' => $trackable->getKey(),
        ]);

        $progress->started_at ??= now();
        // Progress never goes backwards — re-reading a lesson is not a regression.
        $progress->progress_percent = max($progress->progress_percent ?? 0, $percent);
        $progress->status = $progress->status === 'completed' ? 'completed' : $status;
        $progress->last_activity_at = now();

        if ($progress->status === 'completed' && $progress->completed_at === null) {
            $progress->completed_at = now();
        }

        $progress->save();

        return $progress;
    }

    public function completedLessonIds(User $user): array
    {
        return UserProgress::query()
            ->where('user_id', $user->id)
            ->ofType('lesson')
            ->completed()
            ->pluck('trackable_id')
            ->all();
    }

    /** Percent of each module's published lessons the user has completed. */
    public function moduleCompletion(User $user): Collection
    {
        $completed = DB::table('user_progress')
            ->join('lessons', function ($join) {
                $join->on('lessons.id', '=', 'user_progress.trackable_id')
                    ->where('user_progress.trackable_type', '=', 'lesson');
            })
            ->where('user_progress.user_id', $user->id)
            ->where('user_progress.status', 'completed')
            ->select('lessons.learning_module_id', DB::raw('COUNT(*) as done'))
            ->groupBy('lessons.learning_module_id')
            ->pluck('done', 'learning_module_id');

        return LearningModule::query()
            ->active()
            ->withCount(['lessons' => fn (Builder $q) => $q->where('is_published', true)])
            ->with('language:id,name,slug')
            ->orderBy('sort_order')
            ->get()
            ->map(function (LearningModule $module) use ($completed) {
                $done = (int) ($completed[$module->id] ?? 0);
                $total = (int) $module->lessons_count;

                $module->setAttribute('completed_lessons', $done);
                $module->setAttribute('completion_percent', $total > 0 ? (int) round($done / $total * 100) : 0);

                return $module;
            });
    }

    public function xpPerDay(User $user, int $days = 30): Collection
    {
        $rows = XpLog::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays($days - 1)->startOfDay())
            ->selectRaw('DATE(created_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        // Fill the gaps so the chart has one point per day.
        return collect(range($days - 1, 0))
            ->mapWithKeys(function (int $offset) use ($rows) {
                $date = now()->subDays($offset)->toDateString();

                return [$date => (int) ($rows[$date] ?? 0)];
            });
    }

    public function recentActivity(User $user, int $limit = 10): Collection
    {
        return XpLog::query()
            ->where('user_id', $user->id)
            ->with('source')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
