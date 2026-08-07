<?php

declare(strict_types=1);

namespace App\Services\Menu;

use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Versioned cache for rendered navigation trees.
 *
 * Cache *tags* are not available on every store, so invalidation works by
 * bumping a version counter that is baked into every key. Flushing is a single
 * atomic increment — old entries simply become unreachable and expire on TTL.
 */
class MenuCache
{
    public function __construct(private readonly CacheRepository $cache) {}

    public function version(): int
    {
        $key = config('admin.menu.cache_version_key');

        $version = $this->cache->get($key);

        if ($version === null) {
            $version = 1;
            $this->cache->forever($key, $version);
        }

        return (int) $version;
    }

    /**
     * Called by observers whenever menus, roles or permissions change.
     */
    public function flush(): void
    {
        $key = config('admin.menu.cache_version_key');

        try {
            $this->cache->increment($key);
        } catch (\Throwable) {
            // Store without atomic increment support (e.g. file): reset instead.
            $this->cache->forever($key, $this->version() + 1);
        }
    }

    public function key(string $position, string $signature): string
    {
        return sprintf('%s:v%d:%s:%s', config('admin.menu.cache_key'), $this->version(), $position, $signature);
    }

    public function remember(string $position, string $signature, \Closure $callback): array
    {
        return $this->cache->remember(
            $this->key($position, $signature),
            config('admin.menu.cache_ttl'),
            $callback,
        );
    }
}
