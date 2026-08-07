<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Language extends Model
{
    use Auditable;
    use HasFactory;
    use HasSlug;

    protected string $slugSource = 'name';

    protected $fillable = [
        'name', 'slug', 'code', 'flag', 'icon', 'color', 'description', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function modules(): HasMany
    {
        return $this->hasMany(LearningModule::class)->orderBy('sort_order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, LearningModule::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn (Builder $q) => $q
            ->where('name', 'like', "%{$term}%")
            ->orWhere('code', 'like', "%{$term}%"));
    }
}
