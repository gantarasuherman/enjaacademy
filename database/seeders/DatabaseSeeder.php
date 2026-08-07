<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Order matters:
     *   roles/permissions -> users -> learning content (creates module
     *   permissions) -> menus (reads modules and permissions).
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
            AchievementSeeder::class,
            LearningContentSeeder::class,
            QuizSeeder::class,
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
