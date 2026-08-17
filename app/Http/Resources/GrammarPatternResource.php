<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\GrammarPatternExample;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class GrammarPatternResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Collection<int, GrammarPatternExample> $items */
        $items = $this->whenLoaded('items', fn () => $this->items, collect());

        return [
            'id' => $this->slug,
            'title' => $this->title,
            'titleRomaji' => $this->title_romaji,
            'explanation' => $this->explanation,
            'formula' => $this->formula,
            'level' => $this->whenLoaded('category', fn () => $this->category?->level?->name),
            'category' => $this->whenLoaded('category', fn () => $this->category?->name),
            'examples' => $items->where('type', 'example')->values()->map(fn (GrammarPatternExample $e) => [
                'sentence' => $e->sentence,
                'romaji' => $e->romaji,
                'translation' => $e->translation,
            ]),
            'mistakes' => $items->where('type', 'mistake')->values()->map(fn (GrammarPatternExample $e) => [
                'wrong' => $e->sentence,
                'wrongRomaji' => $e->romaji,
                'right' => $e->correction,
                'rightRomaji' => $e->correction_romaji,
                'why' => $e->note,
            ]),
        ];
    }
}
