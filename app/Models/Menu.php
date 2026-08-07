<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

/**
 * @property-read \Illuminate\Support\Collection<int, Menu> $children
 */
class Menu extends Model
{
    use Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'parent_id', 'title', 'slug', 'icon', 'route_name', 'route_params', 'url',
        'type', 'position', 'badge', 'badge_color', 'permission_name', 'role_default',
        'target', 'is_visible', 'is_active', 'is_sidebar', 'is_topbar', 'is_footer',
        'sort_order', 'description',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'is_active' => 'boolean',
            'is_sidebar' => 'boolean',
            'is_topbar' => 'boolean',
            'is_footer' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /* -----------------------------------------------------------------
     | Relations
     | -----------------------------------------------------------------
     */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /** Recursive eager load for unlimited nesting. */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /** Menu Access Matrix (role x menu). */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'menu_role')->withTimestamps();
    }

    /* -----------------------------------------------------------------
     | Scopes
     | -----------------------------------------------------------------
     */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('is_visible', true);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopePosition(Builder $query, string $position): Builder
    {
        return $query->where(match ($position) {
            'topbar' => 'is_topbar',
            'footer' => 'is_footer',
            default => 'is_sidebar',
        }, true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('route_name', 'like', "%{$term}%")
                ->orWhere('permission_name', 'like', "%{$term}%");
        }));
    }

    /* -----------------------------------------------------------------
     | Helpers
     | -----------------------------------------------------------------
     */

    /**
     * Resolve the href. A route name wins over a raw URL; if the route does
     * not exist (module removed, typo in admin) we degrade to '#' instead of
     * throwing and taking the whole layout down.
     */
    public function resolveUrl(): string
    {
        if ($this->type === 'external' && $this->url) {
            return $this->url;
        }

        if ($this->route_name) {
            if (! Route::has($this->route_name)) {
                return '#';
            }

            return route($this->route_name, $this->decodedRouteParams());
        }

        return $this->url ?: '#';
    }

    /** @return array<string, string> */
    public function decodedRouteParams(): array
    {
        if (blank($this->route_params)) {
            return [];
        }

        $decoded = json_decode((string) $this->route_params, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        // Also accept the friendlier "key=value,key2=value2" form.
        return collect(explode(',', (string) $this->route_params))
            ->filter()
            ->mapWithKeys(function (string $pair) {
                [$key, $value] = array_pad(explode('=', $pair, 2), 2, null);

                return [trim((string) $key) => trim((string) $value)];
            })
            ->all();
    }

    public function isClickable(): bool
    {
        return in_array($this->type, ['menu', 'external'], true);
    }

    /** Depth of this node, used to enforce config('admin.menu.max_depth'). */
    public function depth(): int
    {
        $depth = 0;
        $node = $this;

        while ($node->parent_id && $depth < 20) {
            $node = $node->parent()->first();

            if (! $node) {
                break;
            }

            $depth++;
        }

        return $depth;
    }

    /** IDs of this menu and every descendant — used to block cyclic parents. */
    public function descendantIds(): array
    {
        $ids = [$this->id];

        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->descendantIds());
        }

        return $ids;
    }
}
