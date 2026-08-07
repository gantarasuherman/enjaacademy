<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Menu;
use App\Services\Menu\MenuCache;

/**
 * Any write to a menu row invalidates every cached navigation tree.
 */
class MenuObserver
{
    public function __construct(private readonly MenuCache $cache) {}

    public function saved(Menu $menu): void
    {
        $this->cache->flush();
    }

    public function deleted(Menu $menu): void
    {
        $this->cache->flush();
    }

    public function restored(Menu $menu): void
    {
        $this->cache->flush();
    }
}
