<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Generates a unique slug from `$slugSource` when none was supplied.
 */
trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            $source = property_exists($model, 'slugSource') ? $model->slugSource : 'name';

            if (blank($model->slug) && filled($model->{$source})) {
                $model->slug = $model->generateUniqueSlug((string) $model->{$source});
            }
        });
    }

    public function generateUniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: Str::random(8);
        $slug = $base;
        $i = 2;

        while (static::withoutGlobalScopes()
            ->where('slug', $slug)
            ->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()))
            ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
