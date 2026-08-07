<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStat extends Model
{
    protected $fillable = [
        'user_id', 'xp_total', 'level', 'streak_days', 'longest_streak', 'last_active_date',
        'lessons_completed', 'quizzes_completed', 'perfect_quizzes',
        'flashcards_reviewed', 'minutes_learned',
    ];

    protected function casts(): array
    {
        return ['last_active_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Cumulative XP required to *reach* the given level. */
    public static function xpForLevel(int $level): int
    {
        if ($level <= 1) {
            return 0;
        }

        $base = (int) config('admin.gamification.xp_base', 100);
        $exp = (float) config('admin.gamification.xp_exponent', 1.5);

        return (int) round($base * (($level - 1) ** $exp));
    }

    public static function levelForXp(int $xp): int
    {
        $max = (int) config('admin.gamification.max_level', 100);

        for ($level = 1; $level < $max; $level++) {
            if ($xp < self::xpForLevel($level + 1)) {
                return $level;
            }
        }

        return $max;
    }

    public function xpIntoCurrentLevel(): int
    {
        return max(0, $this->xp_total - self::xpForLevel($this->level));
    }

    public function xpNeededForNextLevel(): int
    {
        return max(1, self::xpForLevel($this->level + 1) - self::xpForLevel($this->level));
    }

    public function levelProgressPercent(): int
    {
        return (int) min(100, round($this->xpIntoCurrentLevel() / $this->xpNeededForNextLevel() * 100));
    }
}
