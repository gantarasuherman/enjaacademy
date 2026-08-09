<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\LearningModule;
use App\Models\LessonItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * One system-owned deck (`user_id` null = visible to everyone regardless of
 * `is_public`, per FlashcardDeck::scopeVisibleTo()) per module that already
 * has real lesson content — reuses the existing LessonItem rows rather than
 * duplicating term/reading/meaning text, mirroring exactly how
 * FlashcardRepository::createFromLessonItems() builds cards for a
 * user-created deck.
 */
class FlashcardSeeder extends Seeder
{
    /** @var array<string, array{color:string, icon:string}> */
    private array $deckMeta = [
        'hiragana' => ['color' => 'rose', 'icon' => 'あ'],
        'katakana' => ['color' => 'pink', 'icon' => 'ア'],
        'kanji' => ['color' => 'red', 'icon' => '漢'],
        'kosakata-jepang' => ['color' => 'orange', 'icon' => 'book'],
        'tata-bahasa-jepang' => ['color' => 'amber', 'icon' => 'sitemap'],
        'percakapan-jepang' => ['color' => 'lime', 'icon' => 'chat'],
        'menulis-jepang' => ['color' => 'teal', 'icon' => 'pencil'],
        'membaca-jepang' => ['color' => 'fuchsia', 'icon' => 'newspaper'],
        'jlpt' => ['color' => 'emerald', 'icon' => 'certificate'],
        'bahasa-inggris-umum' => ['color' => 'cyan', 'icon' => 'flag-en'],
        'english-grammar' => ['color' => 'blue', 'icon' => 'sitemap'],
        'english-vocabulary' => ['color' => 'sky', 'icon' => 'book'],
        'listening' => ['color' => 'cyan', 'icon' => 'headphone'],
        'speaking' => ['color' => 'teal', 'icon' => 'microphone'],
        'reading' => ['color' => 'indigo', 'icon' => 'newspaper'],
        'writing' => ['color' => 'violet', 'icon' => 'pencil'],
        'toefl' => ['color' => 'purple', 'icon' => 'certificate'],
        'ielts' => ['color' => 'fuchsia', 'icon' => 'certificate'],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $decks = 0;
            $cards = 0;

            foreach ($this->deckMeta as $slug => $meta) {
                $created = $this->buildDeck($slug, $meta);

                if ($created > 0) {
                    $decks++;
                    $cards += $created;
                }
            }

            $this->command?->info("  flashcard: {$cards} kartu di {$decks} deck");
        });
    }

    /** @param  array{color:string, icon:string}  $meta */
    private function buildDeck(string $moduleSlug, array $meta): int
    {
        $module = LearningModule::where('slug', $moduleSlug)->first();

        if (! $module) {
            return 0;
        }

        /** @var Collection<int, LessonItem> $items */
        $items = LessonItem::query()
            ->whereHas('lesson', fn ($q) => $q->where('learning_module_id', $module->id))
            ->where('is_active', true)
            ->orderBy('lesson_id')
            ->orderBy('sort_order')
            ->get()
            // Skip rows that would produce a blank card back (e.g. a stray item with no reading/romaji/meaning at all).
            ->filter(fn (LessonItem $item) => filled($item->reading) || filled($item->romaji) || filled($item->meaning));

        if ($items->isEmpty()) {
            return 0;
        }

        $deck = FlashcardDeck::updateOrCreate(
            ['slug' => str("Flashcard {$module->name}")->slug()->toString()],
            [
                'user_id' => null,
                'learning_module_id' => $module->id,
                'name' => "Flashcard {$module->name}",
                'description' => "Kartu hafalan otomatis dari seluruh materi {$module->name}.",
                'color' => $meta['color'],
                'icon' => $meta['icon'],
                'is_public' => true,
                'is_active' => true,
            ],
        );

        $deck->cards()->delete();

        $now = now();

        Flashcard::insert($items->values()->map(fn (LessonItem $item, int $i) => [
            'flashcard_deck_id' => $deck->id,
            'lesson_item_id' => $item->id,
            'front' => $item->term,
            'back' => collect([$item->reading, $item->romaji, $item->meaning])->filter()->implode(' — '),
            // `hint` is a varchar(255) — some lesson items (e.g. full email/story
            // "contoh") carry a much longer example, so it has to be truncated.
            'hint' => $item->example ? Str::limit($item->example, 250) : null,
            'audio_path' => $item->audio_path,
            'image_path' => $item->image_path,
            'sort_order' => $i,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all());

        return $items->count();
    }
}
