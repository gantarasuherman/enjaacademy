<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Language;
use App\Models\LearningModule;
use App\Models\Lesson;
use App\Models\LessonItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds languages, learning modules and starter lessons — and generates the
 * `{permission_prefix}.{action}` permissions for each module, exactly the way
 * the admin panel does when someone adds a module later.
 */
class LearningContentSeeder extends Seeder
{
    public function run(): void
    {
        $japanese = Language::updateOrCreate(['slug' => 'japanese'], [
            'name' => 'Bahasa Jepang',
            'code' => 'ja',
            'flag' => '🇯🇵',
            'icon' => 'language',
            'color' => 'rose',
            'description' => 'Hiragana, katakana, kanji, tata bahasa, percakapan, dan persiapan JLPT.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $english = Language::updateOrCreate(['slug' => 'english'], [
            'name' => 'Bahasa Inggris',
            'code' => 'en',
            'flag' => '🇬🇧',
            'icon' => 'language',
            'color' => 'blue',
            'description' => 'Grammar, vocabulary, listening, speaking, reading, writing, TOEFL, dan IELTS.',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $modules = [
            // Japanese
            ['lang' => $japanese, 'name' => 'Hiragana', 'type' => 'kana', 'icon' => 'あ', 'color' => 'rose', 'featured' => true],
            ['lang' => $japanese, 'name' => 'Katakana', 'type' => 'kana', 'icon' => 'ア', 'color' => 'pink', 'featured' => true],
            ['lang' => $japanese, 'name' => 'Kanji', 'type' => 'kanji', 'icon' => '漢', 'color' => 'red', 'featured' => true],
            ['lang' => $japanese, 'name' => 'Kosakata Jepang', 'type' => 'vocabulary', 'icon' => 'book', 'color' => 'orange'],
            ['lang' => $japanese, 'name' => 'Tata Bahasa Jepang', 'type' => 'grammar', 'icon' => 'sitemap', 'color' => 'amber'],
            ['lang' => $japanese, 'name' => 'Percakapan Jepang', 'type' => 'conversation', 'icon' => 'chat', 'color' => 'lime'],
            ['lang' => $japanese, 'name' => 'Menulis Jepang', 'type' => 'writing', 'icon' => 'pencil', 'color' => 'teal'],
            ['lang' => $japanese, 'name' => 'JLPT', 'type' => 'exam', 'icon' => 'certificate', 'color' => 'emerald', 'featured' => true],

            // English
            ['lang' => $english, 'name' => 'English Grammar', 'type' => 'grammar', 'icon' => 'sitemap', 'color' => 'blue'],
            ['lang' => $english, 'name' => 'English Vocabulary', 'type' => 'vocabulary', 'icon' => 'book', 'color' => 'sky', 'featured' => true],
            ['lang' => $english, 'name' => 'Listening', 'type' => 'listening', 'icon' => 'headphone', 'color' => 'cyan'],
            ['lang' => $english, 'name' => 'Speaking', 'type' => 'speaking', 'icon' => 'microphone', 'color' => 'teal'],
            ['lang' => $english, 'name' => 'Reading', 'type' => 'reading', 'icon' => 'newspaper', 'color' => 'indigo'],
            ['lang' => $english, 'name' => 'Writing', 'type' => 'writing', 'icon' => 'pencil', 'color' => 'violet'],
            ['lang' => $english, 'name' => 'TOEFL', 'type' => 'exam', 'icon' => 'certificate', 'color' => 'purple', 'featured' => true],
            ['lang' => $english, 'name' => 'IELTS', 'type' => 'exam', 'icon' => 'certificate', 'color' => 'fuchsia', 'featured' => true],
        ];

        $guard = config('auth.defaults.guard', 'web');
        $actions = config('admin.permission_actions', ['view', 'create', 'update', 'delete']);
        $viewPermissions = [];

        DB::transaction(function () use ($modules, $guard, $actions, &$viewPermissions) {
            foreach ($modules as $index => $definition) {
                $slug = str($definition['name'])->slug()->toString();
                $prefix = str_replace('-', '_', $slug);

                $module = LearningModule::updateOrCreate(['slug' => $slug], [
                    'language_id' => $definition['lang']->id,
                    'name' => $definition['name'],
                    'icon' => $definition['icon'],
                    'color' => $definition['color'],
                    'content_type' => $definition['type'],
                    'permission_prefix' => $prefix,
                    'description' => "Modul {$definition['name']}.",
                    'sort_order' => $index,
                    'is_active' => true,
                    'is_featured' => $definition['featured'] ?? false,
                ]);

                foreach ($actions as $action) {
                    Permission::firstOrCreate(['name' => "{$prefix}.{$action}", 'guard_name' => $guard]);
                }

                $viewPermissions[] = "{$prefix}.view";

                $this->seedLessons($module);
            }
        });

        // Learners can read every module; editors get the full CRUD set.
        Role::where('name', 'Student')->first()?->givePermissionTo($viewPermissions);
        Role::where('name', 'Guest')->first()?->givePermissionTo(['hiragana.view', 'katakana.view']);

        $editorPermissions = Permission::query()
            ->where(function ($q) use ($viewPermissions) {
                foreach ($viewPermissions as $view) {
                    $q->orWhere('name', 'like', str_replace('.view', '.%', $view));
                }
            })
            ->pluck('name');

        Role::where('name', 'Admin')->first()?->givePermissionTo($editorPermissions);
        Role::where('name', 'Teacher')->first()?->givePermissionTo($editorPermissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Modules whose lessons are owned by {@see JapaneseContentSeeder}. Seeding
     * a placeholder here would leave a stray lesson behind once the real
     * content lands, so they are skipped outright.
     *
     * @var array<int, string>
     */
    private array $ownedElsewhere = [
        'hiragana', 'katakana', 'kanji', 'kosakata-jepang',
        'tata-bahasa-jepang', 'percakapan-jepang', 'menulis-jepang',
    ];

    private function seedLessons(LearningModule $module): void
    {
        if (in_array($module->slug, $this->ownedElsewhere, true)) {
            return;
        }

        $blueprint = match ($module->slug) {
            'english-vocabulary' => $this->englishVocabulary(),
            'english-grammar' => $this->englishGrammar(),
            default => $this->genericLesson($module),
        };

        foreach ($blueprint as $order => $definition) {
            $lesson = Lesson::updateOrCreate(
                ['slug' => str($module->slug.'-'.$definition['title'])->slug()->toString()],
                [
                    'learning_module_id' => $module->id,
                    'title' => $definition['title'],
                    'level' => $definition['level'] ?? null,
                    'summary' => $definition['summary'] ?? null,
                    'content' => $definition['content'] ?? null,
                    'estimated_minutes' => $definition['minutes'] ?? 10,
                    'xp_reward' => $definition['xp'] ?? 20,
                    'sort_order' => $order,
                    'is_published' => true,
                    'published_at' => now(),
                ],
            );

            $lesson->items()->delete();

            foreach (array_values($definition['items'] ?? []) as $i => $item) {
                LessonItem::create($item + [
                    'lesson_id' => $lesson->id,
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        }
    }

    /* -----------------------------------------------------------------
     | Content blueprints
     | -----------------------------------------------------------------
     */

    private function hiragana(): array
    {
        $rows = [
            'Vokal Dasar' => [['あ', 'a'], ['い', 'i'], ['う', 'u'], ['え', 'e'], ['お', 'o']],
            'Baris K' => [['か', 'ka'], ['き', 'ki'], ['く', 'ku'], ['け', 'ke'], ['こ', 'ko']],
            'Baris S' => [['さ', 'sa'], ['し', 'shi'], ['す', 'su'], ['せ', 'se'], ['そ', 'so']],
            'Baris T' => [['た', 'ta'], ['ち', 'chi'], ['つ', 'tsu'], ['て', 'te'], ['と', 'to']],
            'Baris N' => [['な', 'na'], ['に', 'ni'], ['ぬ', 'nu'], ['ね', 'ne'], ['の', 'no']],
        ];

        return collect($rows)->map(fn (array $chars, string $title) => [
            'title' => $title,
            'level' => 'N5',
            'summary' => "Mengenal huruf hiragana {$title}.",
            'minutes' => 8,
            'xp' => 15,
            'items' => collect($chars)->map(fn (array $c) => [
                'term' => $c[0],
                'reading' => $c[0],
                'romaji' => $c[1],
                'meaning' => "Dibaca \"{$c[1]}\"",
                'extra' => ['script' => 'hiragana', 'strokes' => null],
            ])->all(),
        ])->values()->all();
    }

    private function katakana(): array
    {
        $rows = [
            'Vokal Dasar' => [['ア', 'a'], ['イ', 'i'], ['ウ', 'u'], ['エ', 'e'], ['オ', 'o']],
            'Baris K' => [['カ', 'ka'], ['キ', 'ki'], ['ク', 'ku'], ['ケ', 'ke'], ['コ', 'ko']],
            'Baris S' => [['サ', 'sa'], ['シ', 'shi'], ['ス', 'su'], ['セ', 'se'], ['ソ', 'so']],
        ];

        return collect($rows)->map(fn (array $chars, string $title) => [
            'title' => $title,
            'level' => 'N5',
            'summary' => "Mengenal huruf katakana {$title}.",
            'minutes' => 8,
            'xp' => 15,
            'items' => collect($chars)->map(fn (array $c) => [
                'term' => $c[0],
                'reading' => $c[0],
                'romaji' => $c[1],
                'meaning' => "Dibaca \"{$c[1]}\"",
                'extra' => ['script' => 'katakana'],
            ])->all(),
        ])->values()->all();
    }

    private function kanji(): array
    {
        return [[
            'title' => 'Kanji Angka',
            'level' => 'N5',
            'summary' => 'Sepuluh kanji angka dasar.',
            'minutes' => 12,
            'xp' => 25,
            'items' => collect([
                ['一', 'いち', 'ichi', 'satu', 1],
                ['二', 'に', 'ni', 'dua', 2],
                ['三', 'さん', 'san', 'tiga', 3],
                ['四', 'よん', 'yon', 'empat', 5],
                ['五', 'ご', 'go', 'lima', 4],
                ['六', 'ろく', 'roku', 'enam', 4],
                ['七', 'なな', 'nana', 'tujuh', 2],
                ['八', 'はち', 'hachi', 'delapan', 2],
                ['九', 'きゅう', 'kyuu', 'sembilan', 2],
                ['十', 'じゅう', 'juu', 'sepuluh', 2],
            ])->map(fn (array $k) => [
                'term' => $k[0],
                'reading' => $k[1],
                'romaji' => $k[2],
                'meaning' => $k[3],
                'example' => "{$k[0]}つ",
                'extra' => ['strokes' => $k[4], 'jlpt' => 'N5'],
            ])->all(),
        ], [
            'title' => 'Kanji Alam',
            'level' => 'N5',
            'summary' => 'Kanji tentang alam dan waktu.',
            'minutes' => 12,
            'xp' => 25,
            'items' => collect([
                ['日', 'ひ', 'hi', 'matahari, hari', 4],
                ['月', 'つき', 'tsuki', 'bulan', 4],
                ['火', 'ひ', 'hi', 'api', 4],
                ['水', 'みず', 'mizu', 'air', 4],
                ['木', 'き', 'ki', 'pohon, kayu', 4],
                ['山', 'やま', 'yama', 'gunung', 3],
                ['川', 'かわ', 'kawa', 'sungai', 3],
            ])->map(fn (array $k) => [
                'term' => $k[0],
                'reading' => $k[1],
                'romaji' => $k[2],
                'meaning' => $k[3],
                'extra' => ['strokes' => $k[4], 'jlpt' => 'N5'],
            ])->all(),
        ]];
    }

    private function englishVocabulary(): array
    {
        return [[
            'title' => 'Travel & Transportation',
            'level' => 'A2',
            'summary' => 'Kosakata penting saat bepergian.',
            'minutes' => 10,
            'xp' => 20,
            'items' => collect([
                ['airport', '/ˈeəpɔːt/', 'bandara', 'We arrived at the airport early.'],
                ['luggage', '/ˈlʌɡɪdʒ/', 'bagasi', 'My luggage is too heavy.'],
                ['departure', '/dɪˈpɑːtʃə/', 'keberangkatan', 'The departure gate is B12.'],
                ['boarding pass', '/ˈbɔːdɪŋ pɑːs/', 'boarding pass', 'Please show your boarding pass.'],
                ['delay', '/dɪˈleɪ/', 'penundaan', 'There is a two-hour delay.'],
            ])->map(fn (array $w) => [
                'term' => $w[0],
                'reading' => $w[1],
                'meaning' => $w[2],
                'example' => $w[3],
                'extra' => ['category' => 'travel'],
            ])->all(),
        ], [
            'title' => 'Business English',
            'level' => 'B1',
            'summary' => 'Kosakata untuk lingkungan kerja profesional.',
            'minutes' => 12,
            'xp' => 25,
            'items' => collect([
                ['deadline', '/ˈdedlaɪn/', 'tenggat waktu', 'The deadline is next Friday.'],
                ['invoice', '/ˈɪnvɔɪs/', 'faktur', 'Please send the invoice by email.'],
                ['stakeholder', '/ˈsteɪkhəʊldə/', 'pemangku kepentingan', 'We need stakeholder approval.'],
                ['revenue', '/ˈrevənjuː/', 'pendapatan', 'Revenue grew by 12% this quarter.'],
            ])->map(fn (array $w) => [
                'term' => $w[0],
                'reading' => $w[1],
                'meaning' => $w[2],
                'example' => $w[3],
                'extra' => ['category' => 'business'],
            ])->all(),
        ]];
    }

    private function englishGrammar(): array
    {
        return [[
            'title' => 'Simple Present Tense',
            'level' => 'A1',
            'summary' => 'Kebiasaan, fakta umum, dan jadwal tetap.',
            'content' => "<p><strong>Rumus:</strong> S + V1 (+s/es) + O</p><p>Digunakan untuk kebiasaan, fakta umum, dan jadwal.</p>",
            'minutes' => 15,
            'xp' => 30,
            'items' => [
                ['term' => 'S + V1 + O', 'meaning' => 'Kalimat positif', 'example' => 'She works in Jakarta.', 'example_meaning' => 'Dia bekerja di Jakarta.'],
                ['term' => 'S + do/does not + V1', 'meaning' => 'Kalimat negatif', 'example' => 'He does not like coffee.', 'example_meaning' => 'Dia tidak suka kopi.'],
                ['term' => 'Do/Does + S + V1?', 'meaning' => 'Kalimat tanya', 'example' => 'Do you speak English?', 'example_meaning' => 'Apakah kamu bisa bahasa Inggris?'],
            ],
        ], [
            'title' => 'Simple Past Tense',
            'level' => 'A2',
            'summary' => 'Peristiwa yang selesai di masa lampau.',
            'content' => '<p><strong>Rumus:</strong> S + V2 + O</p>',
            'minutes' => 15,
            'xp' => 30,
            'items' => [
                ['term' => 'S + V2 + O', 'meaning' => 'Kalimat positif', 'example' => 'They went to Bali last year.', 'example_meaning' => 'Mereka pergi ke Bali tahun lalu.'],
                ['term' => 'S + did not + V1', 'meaning' => 'Kalimat negatif', 'example' => 'I did not see him.', 'example_meaning' => 'Saya tidak melihatnya.'],
            ],
        ]];
    }

    /** Placeholder lesson so every module has at least something to open. */
    private function genericLesson(LearningModule $module): array
    {
        return [[
            'title' => "Pengantar {$module->name}",
            'level' => $module->language->code === 'ja' ? 'N5' : 'A1',
            'summary' => "Materi pembuka untuk modul {$module->name}.",
            'content' => "<p>Materi pembuka modul <strong>{$module->name}</strong>. Tambahkan materi dari panel admin.</p>",
            'minutes' => 10,
            'xp' => 20,
            'items' => [
                ['term' => 'Contoh materi', 'meaning' => 'Ganti melalui panel admin', 'example' => '—'],
            ],
        ]];
    }
}
