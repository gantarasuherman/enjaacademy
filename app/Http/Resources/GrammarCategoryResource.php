<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GrammarCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'patternsCount' => $this->whenLoaded('patterns', fn () => $this->patterns->count(), 0),
            'children' => GrammarCategoryResource::collection($this->whenLoaded('children')),
            'level' => $this->whenLoaded('level', fn () => new GrammarLevelResource($this->level)),
            'parent' => $this->whenLoaded('parent', fn () => $this->parent ? [
                'id' => (string) $this->parent->id,
                'name' => $this->parent->name,
            ] : null),
        ];
    }
}
