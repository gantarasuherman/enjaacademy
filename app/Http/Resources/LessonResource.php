<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\LessonItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'level' => $this->level,
            'summary' => $this->summary,
            'video_url' => $this->video_url,
            'content' => $this->when($this->relationLoaded('items'), fn () => $this->content),
            'translated_content' => $this->when($this->relationLoaded('items'), fn () => $this->translated_content),
            'estimated_minutes' => $this->estimated_minutes,
            'xp_reward' => $this->xp_reward,
            'items_count' => $this->whenCounted('items'),
            'module' => $this->whenLoaded('module', fn () => new ModuleResource($this->module)),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn (LessonItem $item) => [
                'id' => $item->id,
                'term' => $item->term,
                'reading' => $item->reading,
                'romaji' => $item->romaji,
                'meaning' => $item->meaning,
                'example' => $item->example,
                'example_meaning' => $item->example_meaning,
                'extra' => $item->extra,
            ])),
        ];
    }
}
