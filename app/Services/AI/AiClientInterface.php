<?php

declare(strict_types=1);

namespace App\Services\AI;

/**
 * Common shape for any text-generation backend the admin "Buat dengan AI"
 * features (`LessonAiService`, `VocabularyAiService`) can run against —
 * implemented by `GeminiClient` and `GrokClient`. Which one is active is an
 * admin Setting (`ai_provider`), resolved in `AppServiceProvider`.
 */
interface AiClientInterface
{
    public function available(): bool;

    /**
     * Asks the model to return JSON matching `$schema` (Gemini's
     * `responseSchema` shape: `{type: 'OBJECT'|'ARRAY'|'STRING'|..., properties, items, required}`)
     * and decodes it. Throws only after every attempt fails.
     *
     * @param  array<string, mixed>  $schema
     * @return array<int|string, mixed>
     */
    public function generateJson(string $prompt, array $schema, int $attempts = 2): array;

    public function generateText(string $prompt): string;
}
