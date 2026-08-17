<?php

declare(strict_types=1);

namespace App\Services\AI;

use RuntimeException;

/**
 * Fills in the rest of a vocabulary-word form (phonetic, meanings,
 * synonyms/antonyms/collocations, example sentences) from just the word +
 * language + level the admin already typed — used by both "Tambah kata" and
 * "Edit kata" (`VocabularyWordController::generateWithAi()`). Which provider
 * (Gemini/Grok) is active is admin-selectable — see `AiClientInterface`.
 */
class VocabularyAiService
{
    public function __construct(private readonly AiClientInterface $ai) {}

    public function available(): bool
    {
        return $this->ai->available();
    }

    /** @return array{phonetic: string, part_of_speech: string, meaning_id: string, meaning_en: string, synonyms: array<int, string>, antonyms: array<int, string>, collocations: array<int, string>, examples: array<int, array{sentence_en: string, sentence_id: string}>} */
    public function generateWord(string $word, string $languageSlug, ?string $level): array
    {
        if (! $this->available()) {
            throw new RuntimeException('No AI provider is configured.');
        }

        $isJapanese = $languageSlug === 'japanese';
        $level = $level ?: ($isJapanese ? 'N5' : 'Beginner');

        $prompt = $isJapanese
            ? $this->japanesePrompt($word, $level)
            : $this->englishPrompt($word, $level);

        $data = $this->ai->generateJson($prompt, $this->schema());

        return [
            'phonetic' => $this->sanitize($data['phonetic'] ?? '', 100),
            'part_of_speech' => $this->sanitize($data['part_of_speech'] ?? '', 40),
            'meaning_id' => $this->sanitize($data['meaning_id'] ?? '', 500),
            // Only meaningful for English-target words — see form's own help text.
            'meaning_en' => $isJapanese ? '' : $this->sanitize($data['meaning_en'] ?? '', 500),
            'synonyms' => $this->sanitizeList($data['synonyms'] ?? []),
            'antonyms' => $this->sanitizeList($data['antonyms'] ?? []),
            'collocations' => $this->sanitizeList($data['collocations'] ?? []),
            'examples' => collect(array_filter($data['examples'] ?? [], 'is_array'))
                ->map(fn (array $e) => [
                    'sentence_en' => $this->sanitize($e['sentence'] ?? '', 300),
                    'sentence_id' => $this->sanitize($e['sentence_id'] ?? '', 300),
                ])
                ->filter(fn (array $e) => $e['sentence_en'] !== '')
                ->values()
                ->all(),
        ];
    }

    private function englishPrompt(string $word, string $level): string
    {
        return <<<PROMPT
            Kamu membantu admin mengisi data kosakata bahasa Inggris untuk aplikasi belajar bahasa.

            Kata: {$word}
            Level CEFR: {$level}

            Isi:
            - phonetic: transkripsi IPA kata ini
            - part_of_speech: jenis kata singkat dalam bahasa Inggris (noun/verb/adjective/dst)
            - meaning_id: arti dalam Bahasa Indonesia, singkat dan jelas
            - meaning_en: definisi singkat dalam Bahasa Inggris
            - synonyms/antonyms: sinonim/antonim bahasa Inggris yang umum, kalau ada (boleh kosong)
            - collocations: 2-3 kolokasi umum kata ini dalam bahasa Inggris, kalau ada
            - examples: 2 kalimat contoh berbahasa Inggris sesuai level {$level}, masing-masing beserta terjemahan Indonesianya di "sentence_id"

            Jangan mengarang informasi yang salah secara linguistik.
            PROMPT;
    }

    private function japanesePrompt(string $word, string $level): string
    {
        return <<<PROMPT
            Kamu membantu admin mengisi data kosakata bahasa Jepang untuk aplikasi belajar bahasa.

            Kata: {$word}
            Level JLPT: {$level}

            Isi:
            - phonetic: cara baca kata ini dalam romaji (bukan IPA)
            - part_of_speech: jenis kata singkat dalam bahasa Inggris (noun/verb/adjective/dst)
            - meaning_id: arti dalam Bahasa Indonesia, singkat dan jelas
            - meaning_en: kosongkan (string kosong) — tidak dipakai untuk kata Jepang
            - synonyms/antonyms: kata Jepang lain yang semakna/berlawanan, kalau ada (boleh kosong)
            - collocations: 2-3 gabungan kata yang umum dipakai bersama kata ini, kalau ada
            - examples: 2 kalimat contoh berbahasa Jepang (kanji+kana wajar untuk level {$level}), masing-masing beserta terjemahan Indonesianya di "sentence_id"

            Jangan mengarang informasi yang salah secara linguistik.
            PROMPT;
    }

    /** @return array<string, mixed> */
    private function schema(): array
    {
        return [
            'type' => 'OBJECT',
            'properties' => [
                'phonetic' => ['type' => 'STRING'],
                'part_of_speech' => ['type' => 'STRING'],
                'meaning_id' => ['type' => 'STRING'],
                'meaning_en' => ['type' => 'STRING'],
                'synonyms' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                'antonyms' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                'collocations' => ['type' => 'ARRAY', 'items' => ['type' => 'STRING']],
                'examples' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'sentence' => ['type' => 'STRING'],
                            'sentence_id' => ['type' => 'STRING'],
                        ],
                        'required' => ['sentence', 'sentence_id'],
                    ],
                ],
            ],
            'required' => ['meaning_id'],
        ];
    }

    /** @param  array<int, mixed>  $values
     * @return array<int, string> */
    private function sanitizeList(array $values): array
    {
        return collect($values)
            ->filter(fn ($v) => is_string($v))
            ->map(fn (string $v) => $this->sanitize($v, 100))
            ->filter(fn (string $v) => $v !== '')
            ->values()
            ->all();
    }

    private function sanitize(mixed $value, int $maxLength): string
    {
        $value = trim((string) $value);

        return mb_strlen($value) > $maxLength ? '' : $value;
    }
}
