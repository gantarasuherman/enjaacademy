<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Order matters:
     *   roles/permissions -> users -> learning content (creates modules and
     *   permissions) -> Japanese content (fills the modules it owns) ->
     *   menus (reads modules and permissions).
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
            AchievementSeeder::class,
            LearningContentSeeder::class,
            JapaneseContentSeeder::class,
            GrammarStructureSeeder::class,
            GrammarPatternSeeder::class,
            QuizSeeder::class,
            VocabularyWordSeeder::class,
            JapaneseVocabularyImportSeeder::class,
            FlashcardSeeder::class,
            MenuSeeder::class,
        ]);

        $this->command?->newLine();
        $this->command?->info('Seeding complete.');
        $this->command?->table(
            ['Account', 'Email', 'Password'],
            [
                ['Super Admin', 'superadmin@nihongo.test', 'password'],
                ['Admin', 'admin@nihongo.test', 'password'],
                ['Teacher', 'teacher@nihongo.test', 'password'],
                ['Student', 'student@nihongo.test', 'password'],
            ],
        );
    }
}
