<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Modules that exist as *code* (admin screens). Learning modules get their
     * permissions generated from the `learning_modules` rows instead — see
     * LearningContentSeeder — which is what keeps new modules code-free.
     *
     * @var array<string, array<int, string>>
     */
    private array $modules = [
        'management-admin' => ['index', 'update'],
        'menus' => ['view', 'create', 'update', 'delete'],
        'roles' => ['view', 'create', 'update', 'delete'],
        'permissions' => ['view', 'create', 'update', 'delete'],
        'users' => ['view', 'create', 'update', 'delete', 'export'],
        'languages' => ['view', 'create', 'update', 'delete'],
        'modules' => ['view', 'create', 'update', 'delete'],
        'lessons' => ['view', 'create', 'update', 'delete', 'import', 'export'],
        'quizzes' => ['view', 'create', 'update', 'delete'],
        'grammar' => ['view', 'create', 'update', 'delete'],
        'flashcards' => ['view', 'create', 'update', 'delete'],
        'achievements' => ['view', 'create', 'update', 'delete'],
        'enrollments' => ['view', 'delete'],
        'reports' => ['view'],
        'audit-logs' => ['view', 'delete'],
        'backups' => ['view', 'create', 'delete'],
    ];

    /**
     * Role => permission patterns. `*` means every permission of that module.
     *
     * @var array<string, array<int, string>>
     */
    private array $rolePermissions = [
        'Admin' => [
            'management-admin.*', 'menus.*', 'roles.view', 'permissions.view',
            'users.*', 'languages.*', 'modules.*', 'lessons.*', 'quizzes.*', 'grammar.*',
            'flashcards.*', 'achievements.*', 'enrollments.*', 'reports.*', 'audit-logs.view', 'backups.*',
        ],
        'Teacher' => [
            'modules.view', 'languages.view',
            'lessons.view', 'lessons.create', 'lessons.update', 'lessons.import', 'lessons.export',
            'quizzes.view', 'quizzes.create', 'quizzes.update',
            'grammar.view', 'grammar.create', 'grammar.update',
            'flashcards.view', 'flashcards.create', 'flashcards.update',
            'users.view', 'reports.view',
        ],
        'Student' => [],
        'Guest' => [],
    ];

    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        DB::transaction(function () use ($guard) {
            $permissions = [];

            foreach ($this->modules as $module => $actions) {
                foreach ($actions as $action) {
                    $permissions[] = Permission::firstOrCreate([
                        'name' => "{$module}.{$action}",
                        'guard_name' => $guard,
                    ])->name;
                }
            }

            foreach (array_merge([config('admin.super_role')], array_keys($this->rolePermissions)) as $roleName) {
                Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);
            }

            foreach ($this->rolePermissions as $roleName => $patterns) {
                $role = Role::where('name', $roleName)->first();

                $role->syncPermissions($this->expand($patterns, $permissions));
            }

            // The super role deliberately gets no explicit permissions: it is
            // granted everything by Gate::before, so it can never fall behind
            // when a new module adds new permissions.
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info(sprintf(
            '%d permissions across %d roles.',
            Permission::count(),
            Role::count(),
        ));
    }

    /**
     * @param  array<int, string>  $patterns
     * @param  array<int, string>  $all
     * @return array<int, string>
     */
    private function expand(array $patterns, array $all): array
    {
        return collect($patterns)
            ->flatMap(function (string $pattern) use ($all) {
                if (! str_ends_with($pattern, '.*')) {
                    return [$pattern];
                }

                $module = substr($pattern, 0, -2);

                return collect($all)->filter(fn (string $name) => str_starts_with($name, "{$module}."))->all();
            })
            ->unique()
            ->values()
            ->all();
    }
}
