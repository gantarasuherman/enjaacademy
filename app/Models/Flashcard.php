<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'flashcard_deck_id', 'lesson_item_id', 'front', 'back',
        'hint', 'audio_path', 'image_path', 'sort_order',
    ];

    public function deck(): BelongsTo
    {
        return $this->belongsTo(FlashcardDeck::class, 'flashcard_deck_id');
    }

    public function lessonItem(): BelongsTo
    {
        return $this->belongsTo(LessonItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(FlashcardReview::class);
    }

    public function reviewFor(User $user): ?FlashcardReview
    {
        return $this->reviews()->where('user_id', $user->id)->first();
    }

    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }
}
