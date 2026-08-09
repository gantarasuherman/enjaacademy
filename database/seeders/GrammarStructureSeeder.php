<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\GrammarCategory;
use App\Models\GrammarLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Levels and categories are admin-manageable rows, not code — this seeder
 * just gives the app a sensible starting tree (per the JLPT-style structure
 * requested) so there's somewhere to hang GrammarPattern content. Everything
 * here is exactly what an admin could also create by hand from the panel.
 */
class GrammarStructureSeeder extends Seeder
{
    /** @var array<string, array{color: string, description: string}> */
    private array $levels = [
        'N5' => ['color' => 'emerald', 'description' => 'Beginner — dasar kalimat, partikel, dan bentuk kata kerja/kata sifat paling umum.'],
        'N4' => ['color' => 'sky', 'description' => 'Elementary — bentuk potensial, pasif, dan konjungsi bersyarat dasar.'],
        'N3' => ['color' => 'amber', 'description' => 'Intermediate — pola penjelasan alasan, keadaan, dan dugaan.'],
        'N2' => ['color' => 'orange', 'description' => 'Upper Intermediate — pola formal untuk tulisan berita, bisnis, dan esai.'],
        'N1' => ['color' => 'rose', 'description' => 'Advanced — pola akademik, sastra, dan bahasa resmi/idiomatik.'],
    ];

    /** @var array<string, array<int, string>> */
    private array $categories = [
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

    public function run(): void
    {
        $levelCount = 0;
        $categoryCount = 0;

        foreach (array_values(array_keys($this->levels)) as $index => $name) {
            $meta = $this->levels[$name];

            $level = GrammarLevel::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'color' => $meta['color'],
                    'description' => $meta['description'],
                    'sort_order' => $index,
                    'is_active' => true,
                ],
            );
            $levelCount++;

            foreach (array_values($this->categories[$name] ?? []) as $catIndex => $catName) {
                // Looked up by name, not slug — `Str::slug()` collapses a
                // pure-Japanese/symbol name like "です / ます" to nothing, and a
                // fresh random fallback on every run would break idempotency.
                GrammarCategory::firstOrCreate(
                    ['grammar_level_id' => $level->id, 'parent_id' => null, 'name' => $catName],
                    ['slug' => Str::slug($catName) ?: Str::random(8), 'sort_order' => $catIndex, 'is_active' => true],
                );
                $categoryCount++;
            }
        }

        $this->command?->info("  grammar: {$levelCount} level, {$categoryCount} kategori");
    }
}
