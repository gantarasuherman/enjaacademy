<?php

declare(strict_types=1);

namespace Database\Seeders\Concerns;

use App\Models\LearningModule;
use App\Models\Lesson;
use App\Models\LessonItem;
use Illuminate\Support\Str;

/**
 * Shared plumbing for the content seeders.
 *
 * Every seeder that fills a module with lessons uses the same three steps:
 * find the module, upsert its lessons, replace their items. Keeping that here
 * means the Japanese seeders can be almost pure data.
 */
trait SeedsLearningContent
{
    protected function module(string $slug): ?LearningModule
    {
        return LearningModule::where('slug', $slug)->first();
    }

    /**
     * Replace a module's lessons with the given definitions.
     *
     * Lessons are matched by slug so re-running the seeder updates in place
     * rather than duplicating, and items are rewritten wholesale because they
     * are always authored as a complete set.
     *
     * Anything already attached to the module but absent from `$lessons` is
     * pruned. That is what lets this seeder take ownership of a module the
     * generic content seeder had filled with placeholders — no coordination
     * between the two seeders required.
     *
     * @param  array<int, array<string, mixed>>  $lessons
     */
    protected function fill(string $moduleSlug, array $lessons): int
    {
        $module = $this->module($moduleSlug);

        if (! $module) {
            $this->command?->warn("  module [{$moduleSlug}] tidak ditemukan — dilewati");

            return 0;
        }

        $count = 0;
        $keptSlugs = [];

        foreach (array_values($lessons) as $order => $definition) {
            $lesson = Lesson::updateOrCreate(
                ['slug' => Str::slug($moduleSlug.'-'.$definition['title'])],
                [
                    'learning_module_id' => $module->id,
                    'title' => $definition['title'],
                    'level' => $definition['level'] ?? 'N5',
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

            $rows = array_values($definition['items'] ?? []);
            $now = now();

            if ($rows !== []) {
                LessonItem::insert(array_map(fn (array $row, int $i) => [
                    'lesson_id' => $lesson->id,
                    'term' => $row['term'],
                    'reading' => $row['reading'] ?? null,
                    'romaji' => $row['romaji'] ?? null,
                    'meaning' => $row['meaning'] ?? null,
                    'example' => $row['example'] ?? null,
                    'example_meaning' => $row['example_meaning'] ?? null,
                    'extra' => isset($row['extra']) ? json_encode($row['extra'], JSON_UNESCAPED_UNICODE) : null,
                    'sort_order' => $i,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $rows, array_keys($rows)));
            }

            $count += count($rows);
        }

        return $count;
    }

    /**
     * Expand a compact `[char, romaji, meaningWord, meaningTranslation]` row
     * into a full lesson item. Used by the kana seeders, where every entry has
     * the same shape.
     *
     * @param  array<int, array<int, string>>  $rows
     * @return array<int, array<string, mixed>>
     */
    protected function kanaItems(array $rows, string $script): array
    {
        return array_map(fn (array $row) => [
            'term' => $row[0],
            'reading' => $row[0],
            'romaji' => $row[1],
            'meaning' => 'Dibaca "'.$row[1].'"',
            'example' => $row[2] ?? null,
            'example_meaning' => $row[3] ?? null,
            'extra' => ['script' => $script],
        ], $rows);
    }
}
