<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\GrammarCategory;
use App\Models\GrammarLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Levels and categories are admin-manageable rows, not code — this seeder
 * just gives the app a sensible starting tree for each of the four
 * (language, track) trees: Japanese Grammar (JLPT), English Grammar,
 * Japanese sentence Structure, and English sentence Structure. Everything
 * here is exactly what an admin could also create by hand from the panel.
 */
class GrammarStructureSeeder extends Seeder
{
    /** @var array<string, array{color: string, description: string}> */
    private array $jlptLevels = [
        'N5' => ['color' => 'emerald', 'description' => 'Beginner — dasar kalimat, partikel, dan bentuk kata kerja/kata sifat paling umum.'],
        'N4' => ['color' => 'sky', 'description' => 'Elementary — bentuk potensial, pasif, dan konjungsi bersyarat dasar.'],
        'N3' => ['color' => 'amber', 'description' => 'Intermediate — pola penjelasan alasan, keadaan, dan dugaan.'],
        'N2' => ['color' => 'orange', 'description' => 'Upper Intermediate — pola formal untuk tulisan berita, bisnis, dan esai.'],
        'N1' => ['color' => 'rose', 'description' => 'Advanced — pola akademik, sastra, dan bahasa resmi/idiomatik.'],
    ];

    /** @var array<string, array{color: string, description: string}> */
    private array $cefrStyleLevels = [
        'Beginner' => ['color' => 'emerald', 'description' => 'Dasar sekali — kalimat pendek, present tense, dan kosakata sehari-hari.'],
        'Elementary' => ['color' => 'sky', 'description' => 'Bentuk lampau, present continuous, dan modal verb dasar.'],
        'Intermediate' => ['color' => 'amber', 'description' => 'Present perfect, conditional pertama, dan passive voice dasar.'],
        'Upper-Intermediate' => ['color' => 'orange', 'description' => 'Reported speech, conditional kedua, dan passive voice lanjutan.'],
        'Advanced' => ['color' => 'rose', 'description' => 'Pola akademik, inversi, subjunctive, dan struktur formal kompleks.'],
    ];

    /** @var array<string, array<int, string>> */
    private array $japaneseGrammarCategories = [
        'N5' => [
            'Pola Kalimat Dasar', 'です / ます', 'Partikel Dasar', 'Kata Benda',
            'Kata Sifat い', 'Kata Sifat な', 'Bentuk Negatif', 'Bentuk Lampau',
            'Bentuk て', 'Kata Kerja Dasar', 'Waktu', 'Tempat',
            'Keberadaan', 'Keinginan', 'Permintaan', 'Larangan',
        ],
        'N4' => [
            'Bentuk Potensial', 'Bentuk Pasif', 'Bentuk Kausatif Dasar', 'そう',
            'すぎる', 'ながら', 'なら', 'たら', 'ば', 'よう',
        ],
        'N3' => [
            'ように', 'ために', 'わけ', 'ば', 'のに',
            'ことになる', 'ことにする', 'らしい', 'ようだ',
        ],
        'N2' => [
            'わけではない', 'に違いない', 'ものの', 'に基づいて',
            'に対して', 'によって', 'に関して', 'に伴って',
        ],
        'N1' => [
            'Pola Formal', 'Pola Akademik', 'Pola Bisnis', 'Pola Idiomatik',
            'Nuansa Bahasa', 'Bahasa Tertulis', 'Bahasa Resmi',
        ],
    ];

    /** @var array<string, array<int, string>> */
    private array $englishGrammarCategories = [
        'Beginner' => [
            'Simple Present', 'To Be', 'Artikel (a/an/the)', 'Kata Benda Jamak',
            'Kata Ganti Orang', 'Kata Kepemilikan', 'Preposisi Dasar',
            'Kalimat Tanya Dasar', 'Bentuk Negatif', 'There is / There are',
        ],
        'Elementary' => [
            'Simple Past', 'Present Continuous', 'Comparative dan Superlative',
            'Countable dan Uncountable Nouns', 'Modal Verbs (can/could)',
            'Kata Keterangan Frekuensi', 'Preposisi Waktu dan Tempat',
            'Kalimat Perintah', 'Kata Sambung Dasar',
        ],
        'Intermediate' => [
            'Present Perfect', 'Past Continuous', 'Bentuk Masa Depan (will/going to)',
            'First Conditional', 'Modal Verbs (should/must/have to)',
            'Passive Voice Dasar', 'Relative Clauses', 'Gerund dan Infinitive',
            'Phrasal Verbs Dasar',
        ],
        'Upper-Intermediate' => [
            'Past Perfect', 'Second Conditional', 'Passive Voice Lanjutan',
            'Reported Speech', 'Modal Verbs Deduksi', 'Relative Clauses Lanjutan',
            'Used to / Would', 'Causative Form', 'Kata Sambung Lanjutan',
        ],
        'Advanced' => [
            'Third Conditional', 'Mixed Conditionals', 'Subjunctive Mood',
            'Inversion', 'Cleft Sentences', 'Passive Voice Kompleks',
            'Bahasa Akademik Formal', 'Nominalisasi', 'Ellipsis dan Substitusi',
        ],
    ];

    /** @var array<string, array<int, string>> */
    private array $japaneseStructureCategories = [
        'N5' => [
            'Struktur Kalimat SOV', 'Kalimat Tanya', 'Kalimat Majemuk Sederhana',
            'Modifikasi Kata Benda Dasar', 'Kalimat Perbandingan Dasar',
        ],
        'N4' => [
            'Klausa Bersyarat', 'Kalimat Sebab-Akibat', 'Kutipan Sederhana',
            'Klausa Relatif', 'Kalimat Berurutan',
        ],
        'N3' => [
            'Klausa Alasan Kompleks', 'Pengandaian Lanjutan', 'Struktur Kalimat Pasif',
            'Kalimat Majemuk Bertingkat', 'Penekanan dan Fokus Kalimat',
        ],
        'N2' => [
            'Struktur Kalimat Formal Tertulis', 'Klausa Konsesif', 'Struktur Nominalisasi',
            'Inversi Sederhana', 'Struktur Paralel',
        ],
        'N1' => [
            'Struktur Sastra dan Retorika', 'Kalimat Formal Akademik',
            'Kutipan Tidak Langsung Lanjutan', 'Sintaksis Lanjutan', 'Gaya Bahasa Klasik',
        ],
    ];

    /** @var array<string, array<int, string>> */
    private array $englishStructureCategories = [
        'Beginner' => [
            'Urutan Kata Dasar (SVO)', 'Kalimat Sederhana', 'Subjek dan Objek Majemuk',
            'Struktur Kalimat Tanya', 'Struktur Kalimat Negatif',
        ],
        'Elementary' => [
            'Kalimat Majemuk (and/but/or)', 'Kalimat dengan Keterangan Waktu',
            'Klausa Dasar', 'Urutan Kata dengan Adverbia', 'Struktur Perbandingan Dasar',
        ],
        'Intermediate' => [
            'Kalimat Kompleks (because/although/if)', 'Struktur Relative Clause',
            'Noun Clauses', 'Penggabungan Kalimat', 'Struktur Paralel Dasar',
        ],
        'Upper-Intermediate' => [
            'Struktur Reported Speech', 'Struktur Kalimat Pasif',
            'Struktur Kalimat Bersyarat', 'Participle Clauses', 'Cleft Sentences Dasar',
        ],
        'Advanced' => [
            'Struktur Inversi', 'Ellipsis', 'Frasa Nomina Kompleks',
            'Struktur Kalimat Akademik', 'Struktur Paralel Lanjutan',
        ],
    ];

    public function run(): void
    {
        $levelCount = 0;
        $categoryCount = 0;

        $trees = [
            ['language' => 'japanese', 'track' => 'grammar', 'levels' => $this->jlptLevels, 'categories' => $this->japaneseGrammarCategories],
            ['language' => 'english', 'track' => 'grammar', 'levels' => $this->cefrStyleLevels, 'categories' => $this->englishGrammarCategories],
            ['language' => 'japanese', 'track' => 'structure', 'levels' => $this->jlptLevels, 'categories' => $this->japaneseStructureCategories],
            ['language' => 'english', 'track' => 'structure', 'levels' => $this->cefrStyleLevels, 'categories' => $this->englishStructureCategories],
        ];

        foreach ($trees as $tree) {
            [$levels, $categories] = $this->seedTree($tree['language'], $tree['track'], $tree['levels'], $tree['categories']);
            $levelCount += $levels;
            $categoryCount += $categories;
        }

        $this->command?->info("  grammar: {$levelCount} level, {$categoryCount} kategori (4 pohon: grammar JP/EN, struktur JP/EN)");
    }

    /**
     * @param  array<string, array{color: string, description: string}>  $levels
     * @param  array<string, array<int, string>>  $categories
     * @return array{0: int, 1: int} [levelCount, categoryCount]
     */
    private function seedTree(string $language, string $track, array $levels, array $categories): array
    {
        $levelCount = 0;
        $categoryCount = 0;

        foreach (array_values(array_keys($levels)) as $index => $name) {
            $meta = $levels[$name];

            // Keyed by (language, track, name) — not slug — since `Str::slug()`
            // collapses pure-Japanese/symbol names like "です / ます" to nothing,
            // and a fresh random fallback on every run would break idempotency.
            $level = GrammarLevel::firstOrCreate(
                ['language' => $language, 'track' => $track, 'name' => $name],
                [
                    'slug' => Str::slug("{$language}-{$track}-{$name}") ?: Str::random(8),
                    'color' => $meta['color'],
                    'description' => $meta['description'],
                    'sort_order' => $index,
                    'is_active' => true,
                ],
            );
            $levelCount++;

            foreach (array_values($categories[$name] ?? []) as $catIndex => $catName) {
                GrammarCategory::firstOrCreate(
                    ['grammar_level_id' => $level->id, 'parent_id' => null, 'name' => $catName],
                    ['slug' => Str::slug($catName) ?: Str::random(8), 'sort_order' => $catIndex, 'is_active' => true],
                );
                $categoryCount++;
            }
        }

        return [$levelCount, $categoryCount];
    }
}
