<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\LearningModule;
use App\Models\Menu;
use App\Services\Menu\MenuCache;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * Builds the entire navigation as *data*. Nothing here is referenced from a
 * Blade file — the sidebar renders whatever these rows say.
 */
class MenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // forceDelete so a re-seed does not trip the unique slug index on
            // soft-deleted rows left behind by a previous run.
            Menu::query()->withTrashed()->forceDelete();
            $this->usedSlugs = [];

            $this->seed($this->sidebar(), 'sidebar');
            $this->seed($this->footer(), 'footer');
            $this->seed($this->topbar(), 'topbar');
        });

        app(MenuCache::class)->flush();

        $this->command?->info(Menu::count().' menu entries created.');
    }

    /** Slugs already handed out during this run, so duplicates get suffixed. */
    private array $usedSlugs = [];

    /**
     * Titles repeat across sections on purpose ("Pencapaian" exists for both
     * learners and admins), so the slug is de-duplicated rather than assumed
     * unique.
     */
    private function uniqueSlug(string $candidate): string
    {
        $base = str($candidate)->slug()->toString();
        $slug = $base;
        $i = 2;

        while (isset($this->usedSlugs[$slug])) {
            $slug = $base.'-'.$i++;
        }

        $this->usedSlugs[$slug] = true;

        return $slug;
    }

    /**
     * @param  array<int, array<string, mixed>>  $definitions
     */
    private function seed(array $definitions, string $position, ?int $parentId = null): void
    {
        foreach (array_values($definitions) as $order => $definition) {
            $children = $definition['children'] ?? [];
            $roles = $definition['roles'] ?? [];

            unset($definition['children'], $definition['roles']);

            $menu = Menu::create(array_merge([
                'parent_id' => $parentId,
                'type' => 'menu',
                'position' => $position,
                'target' => '_self',
                'is_visible' => true,
                'is_active' => true,
                'is_sidebar' => $position === 'sidebar',
                'is_topbar' => $position === 'topbar',
                'is_footer' => $position === 'footer',
                'sort_order' => $order,
            ], $definition, [
                'slug' => $this->uniqueSlug($position.'-'.($definition['slug'] ?? $definition['title'])),
            ]));

            if ($roles !== []) {
                $menu->roles()->sync(Role::whereIn('name', $roles)->pluck('id'));
            }

            if ($children !== []) {
                $this->seed($children, $position, $menu->id);
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function sidebar(): array
    {
        return [
            // ---------------- Learner section ----------------
            ['title' => 'BELAJAR', 'type' => 'header'],

            [
                'title' => 'Dashboard',
                'icon' => 'home',
                'route_name' => 'peserta.dashboard',
                'description' => 'Ringkasan XP, level, dan progres.',
            ],
            [
                'title' => 'Katalog Modul',
                'icon' => 'grid',
                'route_name' => 'peserta.learning.index',
            ],

            // Every learning module becomes a child entry, generated from the
            // `learning_modules` table and gated by that module's permission.
            [
                'title' => 'Bahasa Jepang',
                'icon' => 'flag-jp',
                'children' => $this->moduleChildren('japanese'),
            ],
            [
                'title' => 'Bahasa Inggris',
                'icon' => 'flag-en',
                'children' => $this->moduleChildren('english'),
            ],

            ['title' => 'Kuis', 'icon' => 'question', 'route_name' => 'peserta.quizzes.index', 'badge' => 'Baru', 'badge_color' => 'emerald'],
            ['title' => 'Flashcard', 'icon' => 'cards', 'route_name' => 'peserta.flashcards.index'],
            ['title' => 'Bookmark', 'icon' => 'bookmark', 'route_name' => 'peserta.bookmarks.index'],
            ['title' => 'Progres', 'icon' => 'trending-up', 'route_name' => 'peserta.progress'],
            ['title' => 'Pencapaian', 'icon' => 'trophy', 'route_name' => 'peserta.achievements'],
            ['title' => 'Papan Peringkat', 'icon' => 'medal', 'route_name' => 'peserta.leaderboard'],

            ['title' => 'divider-learner', 'type' => 'divider'],

            // ---------------- Admin section ----------------
            ['title' => 'ADMINISTRASI', 'type' => 'header', 'permission_name' => 'users.view'],

            [
                'title' => 'Dashboard Admin',
                'icon' => 'gauge',
                'route_name' => 'admin.home',
                'permission_name' => 'users.view',
            ],
            [
                'title' => 'Master Data',
                'icon' => 'database',
                'permission_name' => 'modules.view',
                'children' => [
                    ['title' => 'Bahasa', 'icon' => 'language', 'route_name' => 'admin.languages.index', 'permission_name' => 'languages.view'],
                    ['title' => 'Modul Pembelajaran', 'icon' => 'layers', 'route_name' => 'admin.modules.index', 'permission_name' => 'modules.view'],
                    ['title' => 'Pencapaian', 'icon' => 'medal', 'route_name' => 'admin.achievements.index', 'permission_name' => 'achievements.view'],
                ],
            ],
            [
                'title' => 'Konten Belajar',
                'icon' => 'book-open',
                'permission_name' => 'lessons.view',
                'children' => [
                    ['title' => 'Materi', 'icon' => 'file-text', 'route_name' => 'admin.lessons.index', 'permission_name' => 'lessons.view'],
                    ['title' => 'Materi Video', 'icon' => 'play', 'route_name' => 'admin.video-lessons.index', 'permission_name' => 'lessons.view'],
                    ['title' => 'Kuis', 'icon' => 'clipboard', 'route_name' => 'admin.quizzes.index', 'permission_name' => 'quizzes.view'],
                    ['title' => 'Level Grammar', 'icon' => 'sitemap', 'route_name' => 'admin.grammar.levels.index', 'permission_name' => 'grammar.view'],
                    ['title' => 'Kategori Grammar', 'icon' => 'layers', 'route_name' => 'admin.grammar.categories.index', 'permission_name' => 'grammar.view'],
                    ['title' => 'Pola Grammar', 'icon' => 'book-open', 'route_name' => 'admin.grammar.patterns.index', 'permission_name' => 'grammar.view'],
                    ['title' => 'Kosakata (Kuis Harian)', 'icon' => 'book', 'route_name' => 'admin.vocabulary-words.index', 'permission_name' => 'vocabulary-words.view'],
                ],
            ],
            [
                'title' => 'Pengguna & Akses',
                'icon' => 'shield',
                'permission_name' => 'users.view',
                'children' => [
                    ['title' => 'Pengguna', 'icon' => 'users', 'route_name' => 'admin.users.index', 'permission_name' => 'users.view'],
                    ['title' => 'Role', 'icon' => 'user-check', 'route_name' => 'admin.roles.index', 'permission_name' => 'roles.view'],
                    ['title' => 'Permission', 'icon' => 'key', 'route_name' => 'admin.permissions.index', 'permission_name' => 'permissions.view'],
                    ['title' => 'Matriks Permission', 'icon' => 'table', 'route_name' => 'admin.roles.matrix', 'permission_name' => 'roles.view'],
                ],
            ],
            [
                'title' => 'Menu Management',
                'icon' => 'menu',
                'permission_name' => 'menus.view',
                'children' => [
                    ['title' => 'Daftar Menu', 'icon' => 'list', 'route_name' => 'admin.menus.index', 'permission_name' => 'menus.view'],
                    ['title' => 'Tambah Menu', 'icon' => 'plus', 'route_name' => 'admin.menus.create', 'permission_name' => 'menus.create'],
                    ['title' => 'Matriks Akses Menu', 'icon' => 'table', 'route_name' => 'admin.menus.matrix', 'permission_name' => 'menus.view'],
                ],
            ],
            [
                'title' => 'Laporan',
                'icon' => 'chart',
                'permission_name' => 'reports.view',
                'children' => [
                    ['title' => 'Progres Peserta', 'icon' => 'trending-up', 'route_name' => 'admin.reports.progress', 'permission_name' => 'reports.view'],
                    ['title' => 'Performa Kuis', 'icon' => 'target', 'route_name' => 'admin.reports.quiz', 'permission_name' => 'reports.view'],
                    ['title' => 'Papan Peringkat', 'icon' => 'trophy', 'route_name' => 'admin.reports.leaderboard', 'permission_name' => 'reports.view'],
                    ['title' => 'Aktivitas', 'icon' => 'activity', 'route_name' => 'admin.reports.activity', 'permission_name' => 'reports.view'],
                    ['title' => 'Kelas Diambil', 'icon' => 'user-check', 'route_name' => 'admin.enrollments.index', 'permission_name' => 'enrollments.view'],
                    ['title' => 'Transaksi', 'icon' => 'credit-card', 'route_name' => 'admin.orders.index', 'permission_name' => 'orders.view'],
                ],
            ],
            [
                'title' => 'Sistem',
                'icon' => 'settings',
                'permission_name' => 'management-admin.index',
                'children' => [
                    ['title' => 'Pengaturan', 'icon' => 'sliders', 'route_name' => 'admin.management-admin.index', 'permission_name' => 'management-admin.index'],
                    ['title' => 'Audit Log', 'icon' => 'history', 'route_name' => 'admin.audit-logs.index', 'permission_name' => 'audit-logs.view'],
                    ['title' => 'Backup', 'icon' => 'archive', 'route_name' => 'admin.backups.index', 'permission_name' => 'backups.view'],
                ],
            ],

            // ---------------- Help, pinned to the bottom ----------------
            ['title' => 'divider-help', 'type' => 'divider'],

            [
                'title' => 'Alur Aplikasi',
                'icon' => 'sitemap',
                'route_name' => 'admin.alur',
                'description' => 'Langkah demi langkah untuk tugas yang sering dilakukan.',
                'badge' => 'Baru',
                'badge_color' => 'sky',
            ],
            [
                'title' => 'Panduan',
                'icon' => 'help',
                'route_name' => 'admin.panduan',
                'description' => 'Konsep menu dinamis, RBAC, dan cara menambah modul.',
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function topbar(): array
    {
        return [
            ['title' => 'Beranda', 'route_name' => 'home'],
            ['title' => 'Belajar', 'route_name' => 'peserta.learning.index'],
            ['title' => 'Kuis', 'route_name' => 'peserta.quizzes.index'],
            ['title' => 'Profil', 'route_name' => 'peserta.profile.edit'],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function footer(): array
    {
        return [
            ['title' => 'Tentang Kami', 'route_name' => 'about'],
            ['title' => 'Kontak', 'route_name' => 'contact'],
            ['title' => 'Dokumentasi Laravel', 'type' => 'external', 'url' => 'https://laravel.com/docs', 'target' => '_blank'],
        ];
    }

    /**
     * Turns every module row of a language into a menu child. Because the
     * permission comes from the module itself, adding a module later and
     * pointing a menu at `peserta.learning.module` needs zero code.
     *
     * @return array<int, array<string, mixed>>
     */
    private function moduleChildren(string $languageSlug): array
    {
        return LearningModule::query()
            ->whereHas('language', fn ($q) => $q->where('slug', $languageSlug))
            ->orderBy('sort_order')
            ->get()
            ->map(fn (LearningModule $module) => [
                'title' => $module->name,
                'icon' => $module->icon,
                'route_name' => 'peserta.learning.module',
                'route_params' => json_encode(['module' => $module->slug]),
                'permission_name' => $module->permission('view'),
                'slug' => 'modul-'.$module->slug,
                'description' => $module->description,
            ])
            ->values()
            ->all();
    }
}
