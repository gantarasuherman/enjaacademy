<?php

declare(strict_types=1);

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin wrapper around Groq's OpenAI-compatible chat completions API
 * (https://console.groq.com/docs/openai — runs open models like Llama on
 * Groq's own inference hardware). Genuinely free tier: no credit card at
 * signup, gated only by rate limits (no credits/billing system), unlike
 * Grok's promotional-credit-then-billed model — see `GrokClient`.
 */
class GroqClient implements AiClientInterface
{
    private const BASE_URL = 'https://api.groq.com/openai/v1';

    public function __construct(
        private readonly ?string $apiKey,
        private readonly string $model = 'openai/gpt-oss-120b',
    ) {}

    public function available(): bool
    {
        return filled($this->apiKey);
    }

    public function generateJson(string $prompt, array $schema, int $attempts = 2): array
    {
        if (! $this->available()) {
            throw new RuntimeException('Groq API key is not configured.');
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
        $response = Http::withToken($this->apiKey)
            ->timeout(45)
            ->retry(2, 500)
            ->post(self::BASE_URL.'/chat/completions', [
                'model' => $this->model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => $temperature,
                'max_tokens' => 4096,
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => [
                        'name' => 'response',
                        // Non-strict: strict schema compliance on Groq is only
                        // guaranteed for a couple of specific models (the
                        // openai/gpt-oss family) — every other model, including
                        // the free-tier default here, only best-effort-honours
                        // strict mode. Same non-strict choice as GrokClient.
                        'schema' => $this->toJsonSchema($schema),
                    ],
                ],
            ]);

        $response->throw();

        $choice = data_get($response->json(), 'choices.0');

        if (data_get($choice, 'finish_reason') !== 'stop') {
            throw new RuntimeException('Groq generation did not finish cleanly (truncated or unstable output).');
        }

        $text = data_get($choice, 'message.content');
        $decoded = is_string($text) ? json_decode($text, true) : null;

        if (! is_array($decoded)) {
            throw new RuntimeException('Groq returned a non-JSON or empty response.');
        }

        return $decoded;
    }

    public function generateText(string $prompt): string
    {
        if (! $this->available()) {
            throw new RuntimeException('Groq API key is not configured.');
        }

        $response = Http::withToken($this->apiKey)
            ->timeout(45)
            ->retry(2, 500)
            ->post(self::BASE_URL.'/chat/completions', [
                'model' => $this->model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.4,
            ]);

        $response->throw();

        return (string) data_get($response->json(), 'choices.0.message.content', '');
    }

    /**
     * Gemini's `responseSchema` shape (`type: 'OBJECT'|'ARRAY'|'STRING'|...`,
     * uppercase) is what every caller already writes its schemas in — this
     * lowercases them into standard JSON Schema, identical to GrokClient's
     * converter (both are OpenAI-compatible endpoints expecting the same
     * shape).
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    private function toJsonSchema(array $schema): array
    {
        $type = strtolower((string) ($schema['type'] ?? 'object'));
        $out = ['type' => $type];

        if ($type === 'object' && isset($schema['properties'])) {
            $out['properties'] = collect($schema['properties'])
                ->map(fn (array $prop) => $this->toJsonSchema($prop))
                ->all();
        }

        if ($type === 'array' && isset($schema['items'])) {
            $out['items'] = $this->toJsonSchema($schema['items']);
        }

        if (isset($schema['required'])) {
            $out['required'] = $schema['required'];
        }

        if (isset($schema['enum'])) {
            $out['enum'] = $schema['enum'];
        }

        return $out;
    }
}
