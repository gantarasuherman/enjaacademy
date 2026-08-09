<?php

declare(strict_types=1);

namespace App\Services\AI;

/**
 * Builds the prompts for the admin "Buat dengan AI" lesson-builder feature
 * and reshapes Gemini's structured output into exactly what
 * lesson-builder.js expects for an item (term/reading/romaji/meaning/
 * example/example_meaning/extra) — matching the six item types defined in
 * resources/js/components/lesson-builder/item-types.js.
 */
class LessonAiService
{
    private const ALLOWED_TYPES = ['kana', 'kanji', 'kosakata', 'grammar', 'kalimat'];

    private const MAX_ITEMS = 30;

    public function __construct(private readonly GeminiClient $gemini) {}

    public function available(): bool
    {
        return $this->gemini->available();
    }

    /**
     * @param  array{topic: string, level: ?string, count: int, types: array<string, bool>, language: ?string}  $params
     * @return array<int, array<string, mixed>>
     */
    public function generateItems(array $params): array
    {
        $types = array_values(array_intersect(
            array_keys(array_filter($params['types'] ?? [])),
            self::ALLOWED_TYPES,
        )) ?: self::ALLOWED_TYPES;

        $count = max(1, min(self::MAX_ITEMS, (int) ($params['count'] ?? 10)));
        $language = $params['language'] ?: 'Indonesia';
        $level = $params['level'] ?: 'N5';

        $prompt = <<<PROMPT
            Kamu membuat materi belajar bahasa Jepang untuk aplikasi kursus online.

            Topik: {$params['topic']}
            Level: {$level}
            Jenis item yang diminta: {$this->describeTypes($types)}
            Bahasa penjelasan/arti: {$language}
            Jumlah item: {$count}

            Buat {$count} item pembelajaran sesuai topik dan jenis di atas. Untuk kanji, isi HANYA
            onyomi (katakana) dan kunyomi (hiragana) singkat — JANGAN isi romaji dan JANGAN
            mendaftar banyak alternatif bacaan, cukup satu yang paling umum per field. Untuk kana,
            term adalah karakternya dan romaji cara bacanya (singkat, satu bacaan saja). Untuk
            kosakata, term adalah kosakata Jepang, reading adalah furigana, romaji singkat, meaning
            artinya dalam {$language}. Untuk grammar, term adalah pola kalimatnya dan meaning adalah
            penjelasan singkat. Untuk kalimat, term adalah kalimat lengkap dan meaning terjemahannya.

            Selalu isi contoh (example) dan artinya (example_meaning) secara singkat untuk setiap
            item — untuk kana, contoh berupa satu kata sederhana yang memuat karakter tersebut.
            Setiap field harus singkat (maksimal beberapa kata) — jangan pernah mendaftar banyak
            alternatif atau mengulang kata yang sama berkali-kali. Jangan mengarang informasi yang
            salah secara linguistik.
            PROMPT;

        $items = $this->gemini->generateJson($prompt, $this->itemsSchema($types));

        return array_values(array_map(
            fn (array $raw) => $this->toBuilderItem($raw),
            array_filter($items, 'is_array'),
        ));
    }

    public function generateTranslation(string $content, ?string $level): string
    {
        $level = $level ? " (level {$level})" : '';

        $prompt = <<<PROMPT
            Terjemahkan teks materi pembelajaran bahasa berikut{$level} ke Bahasa Indonesia yang
            natural dan mudah dipahami pembelajar.

            PENTING: teks aslinya terbagi ke beberapa paragraf yang dipisahkan satu baris kosong.
            Terjemahan HARUS punya jumlah paragraf yang sama persis, satu-lawan-satu sesuai urutan
            aslinya (fitur hover-terjemahan di aplikasi memasangkan paragraf berdasarkan urutan
            ini). Jangan menggabung atau memecah paragraf. Jangan tambahkan judul, catatan, atau
            komentar apa pun — kembalikan hanya teks terjemahannya.

            Teks asli:

            {$content}
            PROMPT;

        return trim($this->gemini->generateText($prompt));
    }

    private function describeTypes(array $types): string
    {
        $labels = ['kana' => 'Kana', 'kanji' => 'Kanji', 'kosakata' => 'Kosakata', 'grammar' => 'Grammar', 'kalimat' => 'Kalimat'];

        return implode(', ', array_map(fn ($t) => $labels[$t] ?? $t, $types));
    }

    /** @return array<string, mixed> */
    private function itemsSchema(array $types): array
    {
        return [
            'type' => 'ARRAY',
            'items' => [
                'type' => 'OBJECT',
                'properties' => [
                    'type' => ['type' => 'STRING', 'enum' => $types],
                    'term' => ['type' => 'STRING'],
                    'reading' => ['type' => 'STRING'],
                    'romaji' => ['type' => 'STRING'],
                    'onyomi' => ['type' => 'STRING'],
                    'kunyomi' => ['type' => 'STRING'],
                    'meaning' => ['type' => 'STRING'],
                    'example' => ['type' => 'STRING'],
                    'example_meaning' => ['type' => 'STRING'],
                ],
                'required' => ['type', 'term'],
            ],
        ];
    }

    /** @param  array<string, mixed>  $raw */
    private function toBuilderItem(array $raw): array
    {
        $type = in_array($raw['type'] ?? null, self::ALLOWED_TYPES, true) ? $raw['type'] : 'kosakata';

        $extra = ['type' => $type];

        if ($type === 'kanji') {
            $extra['onyomi'] = $this->sanitize($raw['onyomi'] ?? '', 20);
            $extra['kunyomi'] = $this->sanitize($raw['kunyomi'] ?? '', 20);
        }

        return [
            'term' => $this->sanitize($raw['term'] ?? '', 40),
            // Kanji legitimately have several valid readings — an LLM asked
            // for "the" romaji sometimes spirals into listing all of them.
            // There's no single correct answer to truncate to, so it's
            // dropped outright for kanji rather than shown half-formed.
            'reading' => $type === 'kanji' ? '' : $this->sanitize($raw['reading'] ?? '', 40),
            'romaji' => $type === 'kanji' ? '' : $this->sanitize($raw['romaji'] ?? '', 40),
            'meaning' => $this->sanitize($raw['meaning'] ?? '', 200),
            'example' => $this->sanitize($raw['example'] ?? '', 200),
            'example_meaning' => $this->sanitize($raw['example_meaning'] ?? '', 200),
            'extra' => $extra,
        ];
    }

    /** Defense-in-depth against runaway/repetitive model output — cap length, never crash on it. */
    private function sanitize(mixed $value, int $maxLength): string
    {
        $value = trim((string) $value);

        return mb_strlen($value) > $maxLength ? '' : $value;
    }
}
