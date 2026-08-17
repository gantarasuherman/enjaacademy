<?php

declare(strict_types=1);

namespace App\Services\AI;

use Illuminate\Http\Client\RequestException;
use Throwable;

/**
 * Turns whatever GeminiClient/GrokClient threw into a specific, Indonesian,
 * admin-facing message instead of the one generic "Gagal menghubungi
 * layanan AI" string every AI endpoint used to return regardless of cause
 * (quota exhausted vs no credits vs invalid key vs truncated output all
 * looked identical to the admin, who had no way to tell what to actually do
 * next). Provider name is read off the failed request's host rather than
 * needing `AiClientInterface` to expose one.
 */
class AiErrorTranslator
{
    public static function describe(Throwable $e): string
    {
        if ($e instanceof RequestException) {
            return self::describeHttp($e);
        }

        $message = $e->getMessage();

        return match (true) {
            str_contains($message, 'API key is not configured') => __('Fitur AI belum diaktifkan. Hubungi admin untuk mengatur API key di Pengaturan → Integrasi.'),
            str_contains($message, 'did not finish cleanly') => __('AI berhenti di tengah jawaban (output terpotong). Coba lagi, atau kurangi jumlah item/panjang materi yang diminta.'),
            str_contains($message, 'non-JSON or empty response') => __('AI mengirim balasan yang tidak bisa dibaca. Coba lagi sebentar lagi.'),
            default => __('Gagal menghubungi layanan AI. Coba lagi sebentar lagi.'),
        };
    }

    private static function describeHttp(RequestException $e): string
    {
        $status = $e->response->status();
        $provider = self::providerName($e);
        $detail = self::detail($e);

        return match (true) {
            $status === 401 => __(':provider menolak API key (tidak valid/kedaluwarsa). Perbarui API key di Pengaturan → Integrasi.', ['provider' => $provider]),
            // Groq has no credits/billing system at all (free tier, rate-limit
            // only) — a 403 there always means a bad key, never "out of
            // credits" like Grok/xAI, so it gets the same wording as 401.
            $status === 403 && $provider === 'Groq' => __(':provider menolak API key (tidak valid/kedaluwarsa):detail. Perbarui API key di Pengaturan → Integrasi.', ['provider' => $provider, 'detail' => $detail ? " ({$detail})" : '']),
            $status === 403 => __('Akun :provider belum punya akses/kredit yang cukup:detail. Cek dashboard :provider atau ganti provider aktif di Pengaturan → Integrasi.', ['provider' => $provider, 'detail' => $detail ? " ({$detail})" : '']),
            $status === 429 => __('Kuota :provider untuk hari ini sudah habis, atau terlalu banyak permintaan sekaligus (rate limit). Coba lagi nanti, atau ganti provider AI aktif di Pengaturan → Integrasi.', ['provider' => $provider]),
            in_array($status, [500, 502, 503, 504], true) => __(':provider sedang sibuk/gangguan sementara di sisi mereka. Coba lagi dalam beberapa menit.', ['provider' => $provider]),
            default => __(':provider menolak permintaan (HTTP :status):detail', ['provider' => $provider, 'status' => (string) $status, 'detail' => $detail ? " {$detail}" : '.']),
        };
    }

    private static function providerName(RequestException $e): string
    {
        $host = $e->response->effectiveUri()?->getHost() ?? '';

        return match (true) {
            str_contains($host, 'googleapis.com') => 'Gemini',
            str_contains($host, 'groq.com') => 'Groq',
            str_contains($host, 'x.ai') => 'Grok',
            default => 'Layanan AI',
        };
    }

    /** Best-effort human-readable snippet pulled from the provider's error body — several shapes observed across Gemini/Grok, none guaranteed present. */
    private static function detail(RequestException $e): ?string
    {
        $body = $e->response->json();

        $message = data_get($body, 'error.message') ?? data_get($body, 'error') ?? data_get($body, 'message');

        if (! is_string($message) || $message === '') {
            return null;
        }

        $message = trim(preg_replace('/\s+/', ' ', $message));

        return mb_strlen($message) > 160 ? mb_substr($message, 0, 160).'…' : $message;
    }
}
