<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Language;
use App\Models\LearningModule;
use App\Models\LessonItem;
use App\Models\VocabularyWord;
use App\Models\VocabularyWordExample;
use Illuminate\Database\Seeder;

/**
 * One-time bulk import: the "Kosakata Jepang" learning module already has
 * ~3,900 vocabulary items authored as `lesson_items` (term/reading/romaji/
 * meaning/example, with JLPT level in `extra.jlpt`). This copies them into
 * the `vocabulary_words` bank — the same table the English Daily Quiz draws
 * from — so Japanese vocabulary is browsable/manageable the same way,
 * without touching the original `lessons`/`lesson_items` content at all.
 *
 * Japanese entries have no natural "English meaning" (the source data only
 * has an Indonesian gloss), so `meaning_en` is left null for these rows —
 * the column was made nullable specifically for this. `phonetic` holds the
 * romaji (there's no separate romaji column on `vocabulary_words`).
 *
 * The Daily Quiz itself is NOT wired to Japanese yet — it stays
 * English-only until per-course targeting exists (see session notes).
 */
class JapaneseVocabularyImportSeeder extends Seeder
{
    private const VALID_LEVELS = ['N5', 'N4', 'N3', 'N2', 'N1'];

    public function run(): void
    {
        $language = Language::where('slug', 'japanese')->first();

        if (! $language) {
            $this->command?->warn('  vocabulary_words (Japanese): skipped, "japanese" language not found.');

            return;
        }

        $module = LearningModule::where('slug', 'kosakata-jepang')->first();

        if (! $module) {
            $this->command?->warn('  vocabulary_words (Japanese): skipped, "kosakata-jepang" module not found.');

            return;
        }

        $lessonIds = $module->lessons()->pluck('id');
        $items = LessonItem::whereIn('lesson_id', $lessonIds)->orderBy('id')->get();

        $count = 0;
        $seenTerms = [];

        foreach ($items as $item) {
            $term = trim((string) $item->term);

            // Some terms repeat across lessons (same word taught in more
            // than one category) — first occurrence wins, matching the
            // one-row-per-word shape the bank/Daily Quiz schema expects.
            if ($term === '' || isset($seenTerms[$term])) {
                continue;
            }

            $seenTerms[$term] = true;

            $level = $item->extra['jlpt'] ?? null;
            $level = in_array($level, self::VALID_LEVELS, true) ? $level : 'N5';

            $word = VocabularyWord::updateOrCreate(
                ['language_id' => $language->id, 'word' => $term],
                [
                    'phonetic' => $item->romaji,
                    'part_of_speech' => null,
                    'meaning_id' => $item->meaning ?? '',
                    'meaning_en' => null,
                    'level' => $level,
                    'synonyms' => [],
                    'antonyms' => [],
                    'collocations' => [],
                ],
            );

            $word->examples()->delete();

            if (filled($item->example)) {
                VocabularyWordExample::create([
                    'vocabulary_word_id' => $word->id,
                    'sentence_en' => $item->example,
                    'sentence_id' => $item->example_meaning,
                    'sort_order' => 0,
                ]);
            }

            $count++;
        }

        $this->command?->info("  vocabulary_words (Japanese import): {$count} kata diimpor dari kosakata-jepang");
    }
}
