<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'color' => $this->color,
            'content_type' => $this->content_type,
            'description' => $this->description,
            'lessons_count' => $this->whenCounted('lessons'),
            'language' => $this->whenLoaded('language', fn () => [
                'name' => $this->language->name,
                'slug' => $this->language->slug,
                'flag' => $this->language->flag,
            ]),
        ];
    }
}
