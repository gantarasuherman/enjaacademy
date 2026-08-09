<?php

declare(strict_types=1);

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin wrapper around Google's Gemini REST API (generativelanguage.googleapis.com).
 * Free tier via https://aistudio.google.com/apikey — no billing account required.
 */
class GeminiClient
{
    public function __construct(
        private readonly ?string $apiKey,
        private readonly string $model = 'gemini-2.0-flash',
    ) {}

    public function available(): bool
    {
        return filled($this->apiKey);
    }

    /**
     * Asks Gemini to return JSON matching the given schema (Gemini's
     * structured-output mode) and decodes it. Throws only after every
     * attempt fails — the caller decides how to degrade (see LessonAiService).
     *
     * The currently available free-tier model occasionally spirals into
     * repeating a token forever instead of picking one (observed even on
     * simple, unambiguous fields, not just ambiguous kanji readings) —
     * this is probabilistic per call, not deterministic per prompt, so a
     * fresh regeneration attempt with a lower temperature reliably
     * recovers within a couple of tries rather than needing a different
     * model or a cleverer prompt.
     *
     * @param  array<string, mixed>  $schema
     * @return array<int|string, mixed>
     */
    public function generateJson(string $prompt, array $schema, int $attempts = 2): array
    {
        if (! $this->available()) {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        $lastError = null;

        for ($attempt = 0; $attempt < $attempts; $attempt++) {
            try {
                return $this->requestJson($prompt, $schema, temperature: max(0.1, 0.6 - $attempt * 0.25));
            } catch (RuntimeException $e) {
                $lastError = $e;
            }
        }

        throw $lastError;
    }

    /** @param  array<string, mixed>  $schema */
    private function requestJson(string $prompt, array $schema, float $temperature): array
    {
        $response = Http::timeout(45)
            ->retry(2, 500)
            ->post($this->endpoint(), [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'responseSchema' => $schema,
                    'temperature' => $temperature,
                    'maxOutputTokens' => 4096,
                    // Extended "thinking" occasionally leaks reasoning text into
                    // structured-output fields on models that support it — this
                    // call wants clean data extraction, not reasoning.
                    'thinkingConfig' => ['thinkingLevel' => 'low'],
                ],
            ]);

        $response->throw();

        $candidate = data_get($response->json(), 'candidates.0');

        // A non-STOP finish (most commonly MAX_TOKENS) means generation was
        // cut off mid-answer, usually mid-repetition-loop. The JSON can
        // still happen to parse (e.g. only the first array element got cut
        // cleanly), so this check has to run before trusting decoded data.
        if (data_get($candidate, 'finishReason') !== 'STOP') {
            throw new RuntimeException('Gemini generation did not finish cleanly (truncated or unstable output).');
        }

        $text = data_get($candidate, 'content.parts.0.text');

        $decoded = is_string($text) ? json_decode($text, true) : null;

        if (! is_array($decoded)) {
            throw new RuntimeException('Gemini returned a non-JSON or empty response.');
        }

        return $decoded;
    }

    public function generateText(string $prompt): string
    {
        if (! $this->available()) {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        $response = Http::timeout(45)
            ->retry(2, 500)
            ->post($this->endpoint(), [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.4],
            ]);

        $response->throw();

        return (string) data_get($response->json(), 'candidates.0.content.parts.0.text', '');
    }

    private function endpoint(): string
    {
        return "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
    }
}
