<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GrammarLevelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->slug,
            'name' => $this->name,
            'color' => $this->color,
            'description' => $this->description,
            'categories' => GrammarCategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
