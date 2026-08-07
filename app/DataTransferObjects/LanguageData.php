<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class LanguageData extends BaseData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $slug,
        public readonly string $code,
        public readonly ?string $flag,
        public readonly ?string $icon,
        public readonly string $color,
        public readonly ?string $description,
        public readonly int $sortOrder,
        public readonly bool $isActive,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $name = (string) $request->string('name');

        return new static(
            name: $name,
            slug: $request->filled('slug') ? Str::slug((string) $request->string('slug')) : Str::slug($name),
            code: (string) $request->string('code'),
            flag: $request->input('flag'),
            icon: $request->input('icon'),
            color: (string) $request->input('color', 'indigo'),
            description: $request->input('description'),
            sortOrder: (int) $request->input('sort_order', 0),
            isActive: $request->boolean('is_active'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'flag' => $this->flag,
            'icon' => $this->icon,
            'color' => $this->color,
            'description' => $this->description,
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
        ];
    }
}
