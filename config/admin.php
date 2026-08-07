<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Admin URL prefix
    |--------------------------------------------------------------------------
    | Every admin route is registered under this prefix with the `admin.` name
    | prefix. Change it in .env and the whole panel moves — including the menus
    | rendered from the database, because they resolve by route *name*.
    */
    'prefix' => env('ADMIN_PREFIX', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Super role
    |--------------------------------------------------------------------------
    | Gate::before grants this role everything. It is intentionally the only
    | role name referenced anywhere in code; all other roles live in the DB.
    */
    'super_role' => env('ADMIN_SUPER_ROLE', 'Super Admin'),

    'per_page' => (int) env('ADMIN_PER_PAGE', 15),

    /*
    |--------------------------------------------------------------------------
    | Learner SPA
    |--------------------------------------------------------------------------
    | The React app in frontend/. In production its build is copied into
    | public/app; in development it runs on its own Vite server and Laravel
    | redirects learner routes there.
    */
    'spa_dev_url' => env('SPA_DEV_URL', 'http://localhost:5174'),

    /*
    |--------------------------------------------------------------------------
    | Menu
    |--------------------------------------------------------------------------
    */
    'menu' => [
        'cache_key' => 'menu',
        'cache_version_key' => 'menu:version',
        'cache_ttl' => (int) env('ADMIN_MENU_CACHE_TTL', 86400),

        'positions' => [
            'sidebar' => 'Sidebar',
            'topbar' => 'Topbar',
            'footer' => 'Footer',
        ],

        'types' => [
            'menu' => 'Menu',
            'header' => 'Header / Section title',
            'divider' => 'Divider',
            'external' => 'External link',
        ],

        'targets' => [
            '_self' => 'Same tab',
            '_blank' => 'New tab',
        ],

        'badge_colors' => [
            'gray', 'red', 'orange', 'amber', 'yellow', 'lime', 'green',
            'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet',
            'purple', 'fuchsia', 'pink', 'rose',
        ],

        'max_depth' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission actions
    |--------------------------------------------------------------------------
    | Used by the permission generator and the permission matrix grouping.
    | Permission names follow `{module}.{action}`.
    */
    'permission_actions' => ['view', 'create', 'update', 'delete', 'export', 'import'],

    /*
    |--------------------------------------------------------------------------
    | Admin panel modules
    |--------------------------------------------------------------------------
    | Holding a permission from one of these modules is what grants entry to
    | the admin shell. Learner-facing module permissions (hiragana.view and
    | friends) are deliberately excluded — a student holds plenty of those and
    | must still not reach the panel.
    */
    'panel_modules' => [
        'management-admin', 'menus', 'roles', 'permissions', 'users',
        'languages', 'modules', 'lessons', 'quizzes', 'flashcards',
        'achievements', 'reports', 'audit-logs', 'backups',
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit log
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled' => true,
        'prune_days' => 180,
        'hidden_attributes' => ['password', 'remember_token', 'two_factor_secret'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup
    |--------------------------------------------------------------------------
    */
    'backup' => [
        'disk' => 'local',
        'path' => 'backups',
        'keep' => 10,
        'mysqldump_binary' => env('MYSQLDUMP_BINARY', 'mysqldump'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gamification
    |--------------------------------------------------------------------------
    | Level curve: level N requires base * (N ^ exponent) cumulative XP.
    */
    'gamification' => [
        'xp_base' => 100,
        'xp_exponent' => 1.5,
        'max_level' => 100,
        'xp' => [
            'lesson_completed' => 20,
            'quiz_passed' => 50,
            'quiz_perfect_bonus' => 25,
            'flashcard_reviewed' => 2,
            'daily_streak' => 10,
        ],
    ],
];
