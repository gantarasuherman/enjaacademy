<?php

declare(strict_types=1);

namespace App\Services\AI;

/**
 * Builds the prompts for the admin "Buat dengan AI" lesson-builder feature
 * and reshapes the active provider's structured output into exactly what
 * lesson-builder.js expects for an item (term/reading/romaji/meaning/
 * example/example_meaning/extra) — matching the six item types defined in
 * resources/js/components/lesson-builder/item-types.js. Which provider
 * (Gemini/Grok) is active is admin-selectable — see `AiClientInterface`.
 */
class LessonAiService
{
    private const ALLOWED_TYPES = ['kana', 'kanji', 'kosakata', 'grammar', 'kalimat'];

    private const MAX_ITEMS = 30;

    public function __construct(private readonly AiClientInterface $ai) {}

    public function available(): bool
    {
        return $this->ai->available();
    }

    /** Roughly ~1k tokens — enough for real context without ballooning cost/latency (especially on paid providers like Grok). */
    private const MAX_CONTENT_CONTEXT = 4000;

    /**
     * @param  array{topic: string, level: ?string, count: int, types: array<string, bool>, language: ?string, content: ?string}  $params
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

        // "Informasi Dasar" (title/level) always seeds `topic`/`level` from the
        // client side — this is what makes item generation a "recommendation"
        // instead of a from-scratch request. `content` (Isi Materi), when
        // present, goes further: the model is told to prefer extracting what
        // actually appears in the text over free-associating from the topic.
        $content = trim((string) ($params['content'] ?? ''));
        $contentSection = $content !== ''
            ? "\n\nIsi materi pelajaran ini (jadikan acuan utama — utamakan kosakata/pola yang benar-benar MUNCUL di teks ini, bukan sekadar terkait topik secara umum):\n".mb_substr($content, 0, self::MAX_CONTENT_CONTEXT)."\n"
            : '';

        $prompt = <<<PROMPT
            Kamu membuat materi belajar bahasa Jepang untuk aplikasi kursus online.

            Topik: {$params['topic']}
            Level: {$level}
            Jenis item yang diminta: {$this->describeTypes($types)}
            Bahasa penjelasan/arti: {$language}
            Jumlah item: {$count}{$contentSection}

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

        $items = $this->ai->generateJson($prompt, $this->itemsSchema($types));

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

        return trim($this->ai->generateText($prompt));
    }

    /**
     * Fills "Ringkasan" + "Isi Materi" from just the title (+ optional level
     * and free-text focus instruction) the admin already typed.
     *
     * @return array{summary: string, content: string}
     */
    public function generateContent(string $title, ?string $contentType, ?string $level, ?string $focus, ?string $language = null): array
    {
        $level = $level ?: 'umum';
        $focusLine = $focus ? "\nFokus/instruksi tambahan dari admin: {$focus}" : '';

        // "Informasi Dasar" (Judul/Level/Modul) always seeds the prompt from
        // what the admin already typed/selected — the module's target
        // language especially matters here, since a title like "Reading" or
        // "Percakapan Sehari-hari" alone doesn't say which language's
        // vocabulary/grammar the content should actually use.
        $languageLine = $language ? "\nBahasa yang dipelajari (subjek materi): {$language}" : '';

        // Reading is the one content_type whose `content` field has a real
        // structural constraint downstream — ReadingDetailPage.tsx splits on
        // a literal blank line to pair each paragraph with its translation.
        $formatNote = $contentType === 'reading'
            ? "\n\nPENTING: pisahkan tiap paragraf dengan SATU BARIS KOSONG (bukan hanya ganti baris) — dipakai fitur hover-terjemahan di aplikasi. Tulis 3-5 paragraf yang mengalir sebagai satu bacaan utuh, bukan poin-poin terpisah."
            : '';

        $prompt = <<<PROMPT
            Kamu membuat materi belajar bahasa untuk aplikasi kursus online.

            Judul materi: {$title}
            Level: {$level}
            Tipe materi: {$contentType}{$languageLine}{$focusLine}

            Isi:
            - summary: ringkasan singkat 1-2 kalimat tentang materi ini (ditampilkan sebagai deskripsi singkat di daftar materi)
            - content: isi materi lengkap — penjelasan/bacaan yang sesuai dengan judul, tipe, dan level di atas, dalam Bahasa Indonesia yang jelas dan mudah dipahami pembelajar. Kosakata/kalimat/karakter contoh yang dipakai HARUS dalam bahasa yang dipelajari di atas (kalau disebutkan), bukan bahasa lain.{$formatNote}

            Jangan mengarang informasi yang salah secara linguistik atau faktual.
            PROMPT;

        $data = $this->ai->generateJson($prompt, [
            'type' => 'OBJECT',
            'properties' => [
                'summary' => ['type' => 'STRING'],
                'content' => ['type' => 'STRING'],
            ],
            'required' => ['summary', 'content'],
        ]);

        return [
            'summary' => trim((string) ($data['summary'] ?? '')),
            'content' => trim((string) ($data['content'] ?? '')),
        ];
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
