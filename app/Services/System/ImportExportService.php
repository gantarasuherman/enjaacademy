<?php

declare(strict_types=1);

namespace App\Services\System;

use App\Models\Language;
use App\Models\Lesson;
use App\Models\VocabularyWord;
use App\Repositories\Contracts\LessonItemRepositoryInterface;
use App\Services\Audit\AuditLogger;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * CSV import/export with no third-party dependency — streamed so a large
 * export never has to fit in memory.
 */
class ImportExportService
{
    public function __construct(
        private readonly LessonItemRepositoryInterface $items,
        private readonly AuditLogger $audit,
    ) {}

    /**
     * @param  iterable<int, array<string, mixed>|Arrayable>  $rows
     * @param  array<int, string>  $headings
     */
    public function streamCsv(string $filename, array $headings, iterable $rows): StreamedResponse
    {
        $this->audit->event('exported', __('Exported :file', ['file' => $filename]));

        return response()->streamDownload(function () use ($headings, $rows) {
            $handle = fopen('php://output', 'wb');

            // BOM so Excel opens UTF-8 (kana, kanji) correctly.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headings);

            foreach ($rows as $row) {
                $row = $row instanceof Arrayable ? $row->toArray() : (array) $row;

                fputcsv($handle, array_map(
                    fn ($value) => is_array($value) ? json_encode($value) : $value,
                    array_values($row),
                ));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * Parse an uploaded CSV into associative rows keyed by its header line.
     *
     * @return Collection<int, array<string, string>>
     */
    public function parseCsv(UploadedFile $file, array $requiredColumns = []): Collection
    {
        $handle = fopen($file->getRealPath(), 'rb');

        if ($handle === false) {
            throw ValidationException::withMessages(['file' => __('The uploaded file could not be read.')]);
        }

        $headings = fgetcsv($handle);

        if ($headings === false) {
            fclose($handle);

            throw ValidationException::withMessages(['file' => __('The file is empty.')]);
        }

        // Strip a UTF-8 BOM off the first heading if Excel added one.
        $headings[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headings[0]);
        $headings = array_map(fn ($h) => strtolower(trim((string) $h)), $headings);

        $missing = array_diff($requiredColumns, $headings);

        if ($missing !== []) {
            fclose($handle);

            throw ValidationException::withMessages([
                'file' => __('Missing required column(s): :columns', ['columns' => implode(', ', $missing)]),
            ]);
        }

        $rows = collect();

        while (($line = fgetcsv($handle)) !== false) {
            if ($line === [null] || $line === []) {
                continue;
            }

            $rows->push(array_combine(
                $headings,
                array_pad(array_slice($line, 0, count($headings)), count($headings), null),
            ));
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Bulk-load vocabulary/kanji rows into a lesson.
     *
     * @return int number of items created
     */
    public function importLessonItems(Lesson $lesson, UploadedFile $file): int
    {
        $rows = $this->parseCsv($file, ['term']);

        $count = $this->items->insertMany($lesson->id, $rows->map(fn (array $row) => [
            'term' => trim((string) $row['term']),
            'reading' => $row['reading'] ?? null,
            'romaji' => $row['romaji'] ?? null,
            'meaning' => $row['meaning'] ?? null,
            'example' => $row['example'] ?? null,
            'example_meaning' => $row['example_meaning'] ?? null,
        ])->all());

        $this->audit->event(
            'imported',
            __(':count item(s) imported into lesson :lesson.', ['count' => $count, 'lesson' => $lesson->title]),
            $lesson,
        );

        return $count;
    }

    /** Header row for the lesson-item import template. */
    public function lessonItemTemplate(): StreamedResponse
    {
        return $this->streamCsv(
            'lesson-items-template.csv',
            ['term', 'reading', 'romaji', 'meaning', 'example', 'example_meaning'],
            [[
                'term' => '日本語',
                'reading' => 'にほんご',
                'romaji' => 'nihongo',
                'meaning' => 'Bahasa Jepang',
                'example' => '日本語を勉強します。',
                'example_meaning' => 'Saya belajar bahasa Jepang.',
            ]],
        );
    }

    /**
     * Bulk-load vocabulary bank rows (`language` is a slug, e.g. "english" /
     * "japanese" — rows whose language doesn't match an existing one are
     * skipped rather than failing the whole import). A row is also skipped
     * (never updated) when its `(language, word)` pair already exists —
     * matched case-/whitespace-insensitively — or repeats an earlier row in
     * the same file, so re-uploading the same sheet twice is a no-op rather
     * than piling up duplicates.
     *
     * @return array{imported: int, skipped: int}
     */
    public function importVocabularyWords(UploadedFile $file): array
    {
        $rows = $this->parseCsv($file, ['language', 'word', 'meaning_id', 'level']);
        $languageIds = Language::pluck('id', 'slug');
        $now = now();

        $existing = VocabularyWord::query()
            ->get(['language_id', 'word'])
            ->map(fn (VocabularyWord $w) => $w->language_id.'|'.Str::lower(trim($w->word)))
            ->flip();

        $seenInFile = [];
        $skipped = 0;

        $payload = $rows
            ->filter(function (array $row) use ($languageIds, $existing, &$seenInFile, &$skipped) {
                if (blank($row['word'] ?? null) || ! $languageIds->has($row['language'] ?? null)) {
                    return false;
                }

                $key = $languageIds[$row['language']].'|'.Str::lower(trim($row['word']));

                if ($existing->has($key) || isset($seenInFile[$key])) {
                    $skipped++;

                    return false;
                }

                $seenInFile[$key] = true;

                return true;
            })
            ->map(fn (array $row) => [
                'language_id' => $languageIds[$row['language']],
                'word' => trim($row['word']),
                'phonetic' => $row['phonetic'] ?: null,
                'part_of_speech' => $row['part_of_speech'] ?: null,
                'meaning_id' => $row['meaning_id'] ?? '',
                'meaning_en' => $row['meaning_en'] ?: null,
                'level' => $row['level'],
                'synonyms' => json_encode($this->splitList($row['synonyms'] ?? null)),
                'antonyms' => json_encode($this->splitList($row['antonyms'] ?? null)),
                'collocations' => json_encode($this->splitList($row['collocations'] ?? null)),
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->values();

        // Chunked — a single multi-row INSERT binds every column of every row as a
        // placeholder, and MySQL rejects a prepared statement past 65,535 of them
        // (12 columns/row here, so anything over ~5,400 rows in one INSERT would
        // hit "Prepared statement contains too many placeholders" on large imports).
        foreach ($payload->chunk(500) as $chunk) {
            VocabularyWord::insert($chunk->all());
        }

        $this->audit->event('imported', __(':count vocabulary word(s) imported, :skipped duplicate(s) skipped.', [
            'count' => $payload->count(),
            'skipped' => $skipped,
        ]));

        return ['imported' => $payload->count(), 'skipped' => $skipped];
    }

    /** Header row for the vocabulary-word import template, one example row per language scale. */
    public function vocabularyWordTemplate(): StreamedResponse
    {
        return $this->streamCsv(
            'vocabulary-words-template.csv',
            ['language', 'word', 'phonetic', 'part_of_speech', 'meaning_id', 'meaning_en', 'level', 'synonyms', 'antonyms', 'collocations'],
            [
                [
                    'language' => 'english',
                    'word' => 'apple',
                    'phonetic' => '/ˈæp.əl/',
                    'part_of_speech' => 'noun',
                    'meaning_id' => 'apel',
                    'meaning_en' => 'a round fruit with red or green skin',
                    'level' => 'Beginner',
                    'synonyms' => '',
                    'antonyms' => '',
                    'collocations' => 'apple pie, apple juice',
                ],
                [
                    'language' => 'japanese',
                    'word' => '林檎',
                    'phonetic' => '',
                    'part_of_speech' => 'noun',
                    'meaning_id' => 'apel',
                    'meaning_en' => '',
                    'level' => 'N5',
                    'synonyms' => '',
                    'antonyms' => '',
                    'collocations' => '',
                ],
            ],
        );
    }

    /** Comma-separated cell -> trimmed string list, mirroring `VocabularyWordRequest::splitList()`. */
    private function splitList(?string $value): array
    {
        if (blank($value)) {
            return [];
        }

        return collect(explode(',', $value))
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => $v !== '')
            ->values()
            ->all();
    }
}
