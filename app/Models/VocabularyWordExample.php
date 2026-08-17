<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VocabularyWordExample extends Model
{
    use HasFactory;

    protected $fillable = ['vocabulary_word_id', 'sentence_en', 'sentence_id', 'sort_order'];

    public function word(): BelongsTo
    {
        return $this->belongsTo(VocabularyWord::class, 'vocabulary_word_id');
    }
}
