<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Japanese\ConversationSeeder;
use Database\Seeders\Japanese\GrammarSeeder;
use Database\Seeders\Japanese\KanaSeeder;
use Database\Seeders\Japanese\KanjiSeeder;
use Database\Seeders\Japanese\ReadingSeeder;
use Database\Seeders\Japanese\VocabularySeeder;
use Database\Seeders\Japanese\WritingSeeder;
use Illuminate\Database\Seeder;

/**
 * Fills every Japanese module with real content.
 *
 * Runs after LearningContentSeeder, which creates the modules and their
 * permissions; this seeder only owns the lessons and items inside them. Split
 * across six focused seeders because the data is large and each script has its
 * own conventions.
 */
class JapaneseContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Konten Bahasa Jepang:');

        $this->call([
            KanaSeeder::class,
            KanjiSeeder::class,
            VocabularySeeder::class,
            GrammarSeeder::class,
            ConversationSeeder::class,
            WritingSeeder::class,
            ReadingSeeder::class,
        ], silent: true);
    }
}
