<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /** @var array<int, array<string, mixed>> */
    private array $achievements = [
        ['name' => 'Langkah Pertama', 'criteria_type' => 'lessons_completed', 'criteria_value' => 1, 'xp_reward' => 10, 'icon' => 'footprints', 'badge_color' => 'sky', 'description' => 'Selesaikan materi pertamamu.'],
        ['name' => 'Pembelajar Rajin', 'criteria_type' => 'lessons_completed', 'criteria_value' => 10, 'xp_reward' => 50, 'icon' => 'book', 'badge_color' => 'blue', 'description' => 'Selesaikan 10 materi.'],
        ['name' => 'Kutu Buku', 'criteria_type' => 'lessons_completed', 'criteria_value' => 50, 'xp_reward' => 200, 'icon' => 'library', 'badge_color' => 'indigo', 'description' => 'Selesaikan 50 materi.'],

        ['name' => 'Kuis Perdana', 'criteria_type' => 'quizzes_completed', 'criteria_value' => 1, 'xp_reward' => 10, 'icon' => 'clipboard', 'badge_color' => 'emerald', 'description' => 'Kerjakan kuis pertamamu.'],
        ['name' => 'Penakluk Kuis', 'criteria_type' => 'quizzes_completed', 'criteria_value' => 25, 'xp_reward' => 150, 'icon' => 'target', 'badge_color' => 'teal', 'description' => 'Kerjakan 25 kuis.'],
        ['name' => 'Nilai Sempurna', 'criteria_type' => 'perfect_quizzes', 'criteria_value' => 1, 'xp_reward' => 40, 'icon' => 'star', 'badge_color' => 'amber', 'description' => 'Raih skor 100 pada sebuah kuis.'],
        ['name' => 'Tanpa Cela', 'criteria_type' => 'perfect_quizzes', 'criteria_value' => 10, 'xp_reward' => 250, 'icon' => 'crown', 'badge_color' => 'yellow', 'description' => 'Raih 10 nilai sempurna.'],

        ['name' => 'Konsisten 3 Hari', 'criteria_type' => 'streak_days', 'criteria_value' => 3, 'xp_reward' => 20, 'icon' => 'flame', 'badge_color' => 'orange', 'description' => 'Belajar 3 hari berturut-turut.'],
        ['name' => 'Konsisten 7 Hari', 'criteria_type' => 'streak_days', 'criteria_value' => 7, 'xp_reward' => 70, 'icon' => 'flame', 'badge_color' => 'red', 'description' => 'Belajar seminggu penuh.'],
        ['name' => 'Konsisten 30 Hari', 'criteria_type' => 'streak_days', 'criteria_value' => 30, 'xp_reward' => 400, 'icon' => 'flame', 'badge_color' => 'rose', 'description' => 'Belajar 30 hari berturut-turut.'],

        ['name' => 'Level 5', 'criteria_type' => 'level', 'criteria_value' => 5, 'xp_reward' => 0, 'icon' => 'chevron-up', 'badge_color' => 'violet', 'description' => 'Capai level 5.'],
        ['name' => 'Level 10', 'criteria_type' => 'level', 'criteria_value' => 10, 'xp_reward' => 0, 'icon' => 'chevrons-up', 'badge_color' => 'purple', 'description' => 'Capai level 10.'],
        ['name' => 'Level 25', 'criteria_type' => 'level', 'criteria_value' => 25, 'xp_reward' => 0, 'icon' => 'rocket', 'badge_color' => 'fuchsia', 'description' => 'Capai level 25.'],

        ['name' => 'Kolektor Kartu', 'criteria_type' => 'flashcards_reviewed', 'criteria_value' => 100, 'xp_reward' => 80, 'icon' => 'cards', 'badge_color' => 'cyan', 'description' => 'Review 100 flashcard.'],
        ['name' => 'Seribu XP', 'criteria_type' => 'xp_total', 'criteria_value' => 1000, 'xp_reward' => 0, 'icon' => 'zap', 'badge_color' => 'lime', 'description' => 'Kumpulkan 1.000 XP.'],
        ['name' => 'Sepuluh Ribu XP', 'criteria_type' => 'xp_total', 'criteria_value' => 10000, 'xp_reward' => 0, 'icon' => 'zap', 'badge_color' => 'green', 'description' => 'Kumpulkan 10.000 XP.'],
    ];

    public function run(): void
    {
        foreach ($this->achievements as $index => $achievement) {
            Achievement::updateOrCreate(
                ['slug' => str($achievement['name'])->slug()->toString()],
                $achievement + ['sort_order' => $index, 'is_active' => true],
            );
        }
    }
}
