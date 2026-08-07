<?php

declare(strict_types=1);

namespace App\Services\System;

use App\Models\Lesson;
use App\Repositories\Contracts\LessonItemRepositoryInterface;
use App\Services\Audit\AuditLogger;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
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
}
