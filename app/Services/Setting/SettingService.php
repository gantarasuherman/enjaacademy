<?php

declare(strict_types=1);

namespace App\Services\Setting;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    private const CACHE_KEY = 'settings:all';

    public function __construct(private readonly SettingRepositoryInterface $settings) {}

    /** @return array<string, mixed> */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => $this->settings->pairs()->all());
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->all(), $key, $default);
    }

    public function group(string $group): array
    {
        return $this->settings->pairs($group)->all();
    }

    public function save(array $values, string $group = 'general'): void
    {
        $this->settings->putMany($values, $group);

        $this->forget();
    }

    /**
     * A blank/null `$value` means "leave the existing secret untouched" —
     * admin UIs for API keys never redisplay the real value, so there is no
     * other way for a save to mean "keep what's there" versus "clear it".
     * Use `clearSecret()` for an explicit "remove key" action.
     */
    public function saveSecret(string $key, ?string $value, string $group = 'general'): void
    {
        if (blank($value)) {
            return;
        }

        $this->settings->put($key, Crypt::encryptString($value), $group, 'secret');
        $this->forget();
    }

    public function clearSecret(string $key, string $group = 'general'): void
    {
        $this->settings->put($key, null, $group, 'secret');
        $this->forget();
    }

    public function hasSecret(string $key): bool
    {
        return filled($this->get($key));
    }

    /** Stores an uploaded logo/favicon and records its path. */
    public function saveFile(string $key, \Illuminate\Http\UploadedFile $file, string $group = 'general'): string
    {
        $existing = $this->get($key);

        if ($existing && Storage::disk('public')->exists($existing)) {
            Storage::disk('public')->delete($existing);
        }

        $path = $file->store('branding', 'public');

        $this->settings->put($key, $path, $group, 'file');
        $this->forget();

        return $path;
    }

    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
