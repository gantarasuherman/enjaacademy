<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /** @var array<int, array<string, string>> */
    private array $accounts = [
        ['name' => 'Super Admin', 'email' => 'superadmin@nihongo.test', 'role' => 'Super Admin', 'username' => 'superadmin'],
        ['name' => 'Administrator', 'email' => 'admin@nihongo.test', 'role' => 'Admin', 'username' => 'admin'],
        ['name' => 'Sensei Yamada', 'email' => 'teacher@nihongo.test', 'role' => 'Teacher', 'username' => 'teacher'],
        ['name' => 'Budi Santoso', 'email' => 'student@nihongo.test', 'role' => 'Student', 'username' => 'student'],
    ];

    public function run(): void
    {
        foreach ($this->accounts as $account) {
            /** @var User $user */
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'locale' => 'id',
                    'timezone' => 'Asia/Jakarta',
                ],
            );

            $user->syncRoles([$account['role']]);
            $user->stat()->firstOrCreate([]);
        }

        // A handful of learners so the dashboard charts and leaderboard have
        // something to show on a fresh install.
        User::factory()
            ->count(12)
            ->create()
            ->each(function (User $user) {
                $user->assignRole('Student');
                $user->stat()->firstOrCreate([
                    'xp_total' => random_int(0, 4000),
                    'level' => random_int(1, 8),
                    'streak_days' => random_int(0, 30),
                    'lessons_completed' => random_int(0, 40),
                    'quizzes_completed' => random_int(0, 25),
                ]);
            });

        $this->command?->warn('Default password for every seeded account: "password"');
    }
}
