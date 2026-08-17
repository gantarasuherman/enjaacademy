<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrammarPatternExample extends Model
{
    use HasFactory;

    protected $fillable = [
        'grammar_pattern_id', 'type', 'sentence', 'romaji', 'translation',
        'correction', 'correction_romaji', 'note', 'sort_order',
    ];

    public function pattern(): BelongsTo
    {
        return $this->belongsTo(GrammarPattern::class, 'grammar_pattern_id');
    }
}
