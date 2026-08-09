<?php

declare(strict_types=1);

namespace App\Support\Roadmap;

/**
 * A read-model of the application's actual features, grounded in the real
 * permission modules, routes, and sidebar groups — not an aspirational
 * backlog. Powers the "Peta Fitur" tab on the Alur Aplikasi page.
 *
 * Every feature here maps to something that already exists in code: a route
 * name, a `{module}.{action}` permission, a controller. Nothing is invented.
 */
class FeatureRoadmap
{
    /**
     * Which of the four core roles hold at least one permission on a given
     * admin permission module. Mirrors database/seeders/RolePermissionSeeder.php
     * — kept here as a static read model because that seeder is a one-shot
     * script, not something read back at request time.
     *
     * @var array<string, array<int, string>>
     */
    private const ADMIN_MODULE_ROLES = [
        'languages' => ['Super Admin', 'Admin', 'Teacher'],
        'modules' => ['Super Admin', 'Admin', 'Teacher'],
        'achievements' => ['Super Admin', 'Admin'],
        'lessons' => ['Super Admin', 'Admin', 'Teacher'],
        'quizzes' => ['Super Admin', 'Admin', 'Teacher'],
        // Note: a `flashcards` permission module also exists (RolePermissionSeeder)
        // but has no admin controller/menu yet — nothing to link to, so it is
        // omitted here rather than pointing at a page that doesn't exist.
        'users' => ['Super Admin', 'Admin', 'Teacher'],
        'roles' => ['Super Admin', 'Admin'],
        'permissions' => ['Super Admin', 'Admin'],
        'menus' => ['Super Admin', 'Admin'],
        'reports' => ['Super Admin', 'Admin', 'Teacher'],
        'enrollments' => ['Super Admin', 'Admin'],
        'management-admin' => ['Super Admin', 'Admin'],
        'audit-logs' => ['Super Admin', 'Admin'],
        'backups' => ['Super Admin', 'Admin'],
    ];

    /**
     * Every learning-module permission (hiragana.view, kanji.create, …) is
     * granted in full to these four roles by LearningContentSeeder — Guest is
     * the only role limited to a subset (hiragana.view + katakana.view), so it
     * is called out as a note rather than given its own lane.
     *
     * @var array<int, string>
     */
    private const LEARNER_ROLES = ['Super Admin', 'Admin', 'Teacher', 'Student'];

    /**
     * @return array<int, array{key: string, label: string, icon: string, features: array<int, array<string, mixed>>}>
     */
    public static function groups(): array
    {
        return [
            [
                'key' => 'master-data',
                'label' => 'Master Data',
                'icon' => 'database',
                'features' => [
                    [
                        'id' => 'languages',
                        'name' => 'Bahasa',
                        'icon' => 'language',
                        'route' => 'admin.languages.index',
                        'permission' => 'languages.view',
                        'description' => 'Daftar bahasa yang diajarkan (Jepang, Inggris, dst.) — dasar dari seluruh hierarki modul pembelajaran.',
                        'actions' => ['view', 'create', 'update', 'delete'],
                        'prerequisites' => [],
                        'related' => ['modules'],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::ADMIN_MODULE_ROLES['languages'],
                    ],
                    [
                        'id' => 'modules',
                        'name' => 'Modul Pembelajaran',
                        'icon' => 'layers',
                        'route' => 'admin.modules.index',
                        'permission' => 'modules.view',
                        'description' => 'Modul seperti Hiragana, Kanji, Kosakata — tiap modul otomatis mendapat permission {prefix}.view/create/update/delete sendiri saat dibuat, tanpa perlu deploy kode baru.',
                        'actions' => ['view', 'create', 'update', 'delete'],
                        'prerequisites' => ['languages'],
                        'related' => ['lessons', 'quizzes', 'katalog-modul'],
                        'flow_id' => 'modul',
                        'extra_routes' => [],
                        'roles' => self::ADMIN_MODULE_ROLES['modules'],
                    ],
                    [
                        'id' => 'achievements',
                        'name' => 'Pencapaian',
                        'icon' => 'medal',
                        'route' => 'admin.achievements.index',
                        'permission' => 'achievements.view',
                        'description' => 'Kriteria badge (XP total, level, streak, jumlah kuis sempurna, dst.) yang otomatis dievaluasi dari statistik peserta (UserStat).',
                        'actions' => ['view', 'create', 'update', 'delete'],
                        'prerequisites' => [],
                        'related' => ['pencapaian-peserta'],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::ADMIN_MODULE_ROLES['achievements'],
                    ],
                ],
            ],

            [
                'key' => 'konten-belajar',
                'label' => 'Konten Belajar',
                'icon' => 'book-open',
                'features' => [
                    [
                        'id' => 'lessons',
                        'name' => 'Materi',
                        'icon' => 'file-text',
                        'route' => 'admin.lessons.index',
                        'permission' => 'lessons.view',
                        'description' => 'Materi berisi kana, kanji, kosakata, atau poin grammar di dalam sebuah modul. Bisa diisi manual atau impor massal lewat CSV.',
                        'actions' => ['view', 'create', 'update', 'delete', 'import', 'export'],
                        'prerequisites' => ['modules'],
                        'related' => ['quizzes', 'belajar'],
                        'flow_id' => 'materi',
                        'extra_routes' => [
                            ['label' => 'Template CSV', 'route' => 'admin.lessons.template'],
                        ],
                        'roles' => self::ADMIN_MODULE_ROLES['lessons'],
                    ],
                    [
                        'id' => 'quizzes',
                        'name' => 'Kuis',
                        'icon' => 'clipboard',
                        'route' => 'admin.quizzes.index',
                        'permission' => 'quizzes.view',
                        'description' => 'Kuis penutup materi: soal, kunci jawaban, dan pembahasan. Bisa dikaitkan ke modul dan materi tertentu, keduanya opsional.',
                        'actions' => ['view', 'create', 'update', 'delete'],
                        'prerequisites' => ['modules'],
                        'related' => ['lessons', 'kuis-peserta', 'reports'],
                        'flow_id' => 'kuis',
                        'extra_routes' => [],
                        'roles' => self::ADMIN_MODULE_ROLES['quizzes'],
                    ],
                ],
            ],

            [
                'key' => 'pengguna-akses',
                'label' => 'Pengguna & Akses',
                'icon' => 'shield',
                'features' => [
                    [
                        'id' => 'users',
                        'name' => 'Pengguna',
                        'icon' => 'users',
                        'route' => 'admin.users.index',
                        'permission' => 'users.view',
                        'description' => 'Akun aplikasi: pembuatan, penonaktifan, penetapan role, dan export daftar pengguna.',
                        'actions' => ['view', 'create', 'update', 'delete', 'export'],
                        'prerequisites' => [],
                        'related' => ['roles'],
                        'flow_id' => 'pengguna',
                        'extra_routes' => [],
                        'roles' => self::ADMIN_MODULE_ROLES['users'],
                    ],
                    [
                        'id' => 'roles',
                        'name' => 'Role',
                        'icon' => 'user-check',
                        'route' => 'admin.roles.index',
                        'permission' => 'roles.view',
                        'description' => 'Kumpulan permission yang dipasangkan ke pengguna. Baris Super Admin sengaja terkunci — role itu memperoleh semua izin lewat Gate::before, bukan dari tabel permission.',
                        'actions' => ['view', 'create', 'update', 'delete'],
                        'prerequisites' => ['permissions'],
                        'related' => ['users', 'permissions'],
                        'flow_id' => 'hak-akses',
                        'extra_routes' => [
                            ['label' => 'Matriks Permission', 'route' => 'admin.roles.matrix'],
                        ],
                        'roles' => self::ADMIN_MODULE_ROLES['roles'],
                    ],
                    [
                        'id' => 'permissions',
                        'name' => 'Permission',
                        'icon' => 'key',
                        'route' => 'admin.permissions.index',
                        'permission' => 'permissions.view',
                        'description' => 'Daftar permission mentah (`{module}.{action}`). Permission untuk modul baru bisa digenerate otomatis lewat tombol Generate.',
                        'actions' => ['view', 'create', 'update', 'delete'],
                        'prerequisites' => [],
                        'related' => ['roles'],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::ADMIN_MODULE_ROLES['permissions'],
                    ],
                ],
            ],

            [
                'key' => 'menu-management',
                'label' => 'Menu Management',
                'icon' => 'menu',
                'features' => [
                    [
                        'id' => 'menus',
                        'name' => 'Menu',
                        'icon' => 'menu',
                        'route' => 'admin.menus.index',
                        'permission' => 'menus.view',
                        'description' => 'Seluruh navigasi sidebar/topbar/footer adalah data di tabel ini, bukan kode Blade — menambah menu baru tidak butuh deploy.',
                        'actions' => ['view', 'create', 'update', 'delete'],
                        'prerequisites' => ['roles'],
                        'related' => ['roles'],
                        'flow_id' => 'menu',
                        'extra_routes' => [
                            ['label' => 'Tambah Menu', 'route' => 'admin.menus.create'],
                            ['label' => 'Matriks Akses Menu', 'route' => 'admin.menus.matrix'],
                        ],
                        'roles' => self::ADMIN_MODULE_ROLES['menus'],
                    ],
                ],
            ],

            [
                'key' => 'laporan',
                'label' => 'Laporan',
                'icon' => 'chart',
                'features' => [
                    [
                        'id' => 'reports',
                        'name' => 'Laporan',
                        'icon' => 'chart',
                        'route' => 'admin.reports.progress',
                        'permission' => 'reports.view',
                        'description' => 'Progres peserta, performa kuis, papan peringkat, dan aktivitas pendaftaran — semua bisa diexport ke CSV.',
                        'actions' => ['view', 'export'],
                        'prerequisites' => ['users', 'lessons', 'quizzes'],
                        'related' => ['users', 'quizzes'],
                        'flow_id' => 'laporan',
                        'extra_routes' => [
                            ['label' => 'Performa Kuis', 'route' => 'admin.reports.quiz'],
                            ['label' => 'Papan Peringkat', 'route' => 'admin.reports.leaderboard'],
                            ['label' => 'Aktivitas', 'route' => 'admin.reports.activity'],
                        ],
                        'roles' => self::ADMIN_MODULE_ROLES['reports'],
                    ],
                    [
                        'id' => 'enrollments',
                        'name' => 'Kelas Diambil',
                        'icon' => 'user-check',
                        'route' => 'admin.enrollments.index',
                        'permission' => 'enrollments.view',
                        'description' => 'Catatan siapa mengambil kelas apa. Ini pendaftaran, bukan gerbang akses — akses tetap diatur lewat permission modul seperti biasa.',
                        'actions' => ['view', 'delete'],
                        'prerequisites' => ['modules'],
                        'related' => ['modules', 'katalog-modul'],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::ADMIN_MODULE_ROLES['enrollments'],
                    ],
                ],
            ],

            [
                'key' => 'sistem',
                'label' => 'Sistem',
                'icon' => 'settings',
                'features' => [
                    [
                        'id' => 'management-admin',
                        'name' => 'Pengaturan',
                        'icon' => 'sliders',
                        'route' => 'admin.management-admin.index',
                        'permission' => 'management-admin.index',
                        'description' => 'Identitas aplikasi dan meta situs (nama, deskripsi, dsb).',
                        'actions' => ['lihat', 'ubah'],
                        'prerequisites' => [],
                        'related' => [],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::ADMIN_MODULE_ROLES['management-admin'],
                    ],
                    [
                        'id' => 'audit-logs',
                        'name' => 'Audit Log',
                        'icon' => 'history',
                        'route' => 'admin.audit-logs.index',
                        'permission' => 'audit-logs.view',
                        'description' => 'Jejak perubahan (create/update/delete) pada model yang memakai trait Auditable — User, Modul, Materi, Bahasa. Retensi 180 hari.',
                        'actions' => ['view', 'delete', 'export'],
                        'prerequisites' => [],
                        'related' => [],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::ADMIN_MODULE_ROLES['audit-logs'],
                    ],
                    [
                        'id' => 'backups',
                        'name' => 'Backup',
                        'icon' => 'archive',
                        'route' => 'admin.backups.index',
                        'permission' => 'backups.view',
                        'description' => 'Backup database (mysqldump) sekali klik atau otomatis harian lewat scheduler. 10 arsip terbaru disimpan.',
                        'actions' => ['view', 'create', 'delete', 'download'],
                        'prerequisites' => [],
                        'related' => [],
                        'flow_id' => 'backup',
                        'extra_routes' => [],
                        'roles' => self::ADMIN_MODULE_ROLES['backups'],
                    ],
                ],
            ],

            [
                'key' => 'belajar-peserta',
                'label' => 'Belajar (Peserta)',
                'icon' => 'home',
                'features' => [
                    [
                        'id' => 'dashboard-peserta',
                        'name' => 'Dashboard Peserta',
                        'icon' => 'home',
                        'route' => 'peserta.dashboard',
                        'permission' => null,
                        'description' => 'Ringkasan XP, level, dan progres belajar peserta yang sedang login.',
                        'actions' => ['view'],
                        'prerequisites' => [],
                        'related' => [],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::LEARNER_ROLES,
                    ],
                    [
                        'id' => 'katalog-modul',
                        'name' => 'Katalog Modul',
                        'icon' => 'grid',
                        'route' => 'peserta.learning.index',
                        'permission' => null,
                        'description' => 'Daftar semua modul yang boleh diakses peserta, per bahasa. Guest hanya melihat Hiragana & Katakana; role lain melihat seluruh modul yang mereka punya izinnya.',
                        'actions' => ['view'],
                        'prerequisites' => ['modules'],
                        'related' => ['belajar'],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::LEARNER_ROLES,
                    ],
                    [
                        'id' => 'belajar',
                        'name' => 'Halaman Belajar',
                        'icon' => 'book-open',
                        'route' => 'peserta.learning.module',
                        'permission' => null,
                        'description' => 'Pemutar materi generik — bentuknya menyesuaikan content_type modul (kana, kanji, vocabulary, grammar, listening, dst.), dilayani SPA React di frontend/.',
                        'actions' => ['view'],
                        'prerequisites' => ['lessons'],
                        'related' => ['katalog-modul', 'kuis-peserta', 'bookmark'],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::LEARNER_ROLES,
                    ],
                    [
                        'id' => 'kuis-peserta',
                        'name' => 'Kuis',
                        'icon' => 'question',
                        'route' => 'peserta.quizzes.index',
                        'permission' => null,
                        'description' => 'Mengerjakan kuis, dinilai di server (kunci jawaban tidak pernah dikirim ke browser).',
                        'actions' => ['view', 'kerjakan'],
                        'prerequisites' => ['quizzes'],
                        'related' => ['belajar', 'progres'],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::LEARNER_ROLES,
                    ],
                    [
                        'id' => 'flashcard-peserta',
                        'name' => 'Flashcard',
                        'icon' => 'cards',
                        'route' => 'peserta.flashcards.index',
                        'permission' => null,
                        'description' => 'Latihan spaced-repetition (SM-2) dari deck sistem atau deck buatan peserta sendiri.',
                        'actions' => ['view', 'review'],
                        'prerequisites' => [],
                        'related' => ['progres'],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::LEARNER_ROLES,
                    ],
                    [
                        'id' => 'bookmark',
                        'name' => 'Bookmark',
                        'icon' => 'bookmark',
                        'route' => 'peserta.bookmarks.index',
                        'permission' => null,
                        'description' => 'Menyimpan materi atau kuis untuk dibuka lagi nanti.',
                        'actions' => ['view', 'tambah', 'hapus'],
                        'prerequisites' => ['belajar'],
                        'related' => ['belajar'],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::LEARNER_ROLES,
                    ],
                    [
                        'id' => 'progres',
                        'name' => 'Progres',
                        'icon' => 'trending-up',
                        'route' => 'peserta.progress',
                        'permission' => null,
                        'description' => 'Status selesai/belum per materi dan modul, plus waktu belajar.',
                        'actions' => ['view'],
                        'prerequisites' => ['belajar', 'kuis-peserta'],
                        'related' => ['pencapaian-peserta', 'papan-peringkat'],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::LEARNER_ROLES,
                    ],
                    [
                        'id' => 'pencapaian-peserta',
                        'name' => 'Pencapaian',
                        'icon' => 'trophy',
                        'route' => 'peserta.achievements',
                        'permission' => null,
                        'description' => 'Badge yang sudah/belum terbuka, dicocokkan otomatis terhadap kriteria yang dibuat admin.',
                        'actions' => ['view'],
                        'prerequisites' => ['achievements', 'progres'],
                        'related' => ['progres'],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::LEARNER_ROLES,
                    ],
                    [
                        'id' => 'papan-peringkat',
                        'name' => 'Papan Peringkat',
                        'icon' => 'medal',
                        'route' => 'peserta.leaderboard',
                        'permission' => null,
                        'description' => 'Peringkat peserta berdasarkan XP.',
                        'actions' => ['view'],
                        'prerequisites' => ['progres'],
                        'related' => [],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::LEARNER_ROLES,
                    ],
                    [
                        'id' => 'profil',
                        'name' => 'Profil',
                        'icon' => 'user-check',
                        'route' => 'peserta.profile.edit',
                        'permission' => null,
                        'description' => 'Data akun dan preferensi peserta sendiri.',
                        'actions' => ['view', 'ubah'],
                        'prerequisites' => [],
                        'related' => [],
                        'flow_id' => null,
                        'extra_routes' => [],
                        'roles' => self::LEARNER_ROLES,
                    ],
                ],
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        return array_merge(...array_map(
            fn (array $group) => $group['features'],
            self::groups(),
        ));
    }

    public static function find(string $id): ?array
    {
        foreach (self::all() as $feature) {
            if ($feature['id'] === $id) {
                return $feature;
            }
        }

        return null;
    }

    /** @return array{total: int, groups: int, roles: int} */
    public static function stats(): array
    {
        $groups = self::groups();

        return [
            'total' => count(self::all()),
            'groups' => count($groups),
            'roles' => count(self::LEARNER_ROLES),
        ];
    }
}
