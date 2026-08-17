<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Enrollment;
use App\Models\LearningModule;
use App\Models\User;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentRepository extends BaseRepository implements EnrollmentRepositoryInterface
{
    protected array $with = ['user:id,name,email', 'learningModule:id,name,slug'];

    protected string $defaultSort = 'created_at';

    protected string $defaultDirection = 'desc';

    protected array $sortable = ['id', 'created_at', 'enrolled_at'];

    public function model(): string
    {
        return Enrollment::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn (Builder $q, $term) => $q->whereHas(
                'user',
                fn (Builder $u) => $u->search($term),
            ))
            ->when($filters['module'] ?? null, fn (Builder $q, $slug) => $q->whereHas(
                'learningModule',
                fn (Builder $m) => $m->where('slug', $slug),
            ));
    }

    public function isEnrolled(User $user, LearningModule $module): bool
    {
        return Enrollment::query()
            ->where('user_id', $user->id)
            ->where('learning_module_id', $module->id)
            ->exists();
    }

    public function toggle(User $user, LearningModule $module): bool
    {
        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('learning_module_id', $module->id)
            ->first();

        if ($enrollment) {
            $enrollment->delete();

            // Unenrolling from the active course hands "active" to whatever
            // enrollment is left (most recent first), or clears it entirely.
            if ($user->active_module_id === $module->id) {
                $next = Enrollment::query()
                    ->where('user_id', $user->id)
                    ->orderByDesc('enrolled_at')
                    ->first();

                $user->update(['active_module_id' => $next?->learning_module_id]);
            }

            return false;
        }

        Enrollment::create([
            'user_id' => $user->id,
            'learning_module_id' => $module->id,
            'enrolled_at' => now(),
        ]);

        // First-ever enrollment becomes the active course automatically.
        if ($user->active_module_id === null) {
            $user->update(['active_module_id' => $module->id]);
        }

        return true;
    }
}
