<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * N5..N1 out of the box, but a row — not an enum — so an admin can add a new
 * level (or a non-JLPT scheme entirely) without a deploy.
 */
class GrammarLevel extends Model
{
    use HasFactory;
    use HasSlug;

    protected string $slugSource = 'name';

    protected $fillable = ['name', 'slug', 'color', 'description', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(GrammarCategory::class);
    }
}
