<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class FlashcardDeckData extends BaseData
{
    public function __construct(
        public readonly ?int $userId,
        public readonly ?int $moduleId,
        public readonly string $name,
        public readonly ?string $slug,
        public readonly ?string $description,
        public readonly string $color,
        public readonly ?string $icon,
        public readonly bool $isPublic,
        public readonly bool $isActive,
        /** Lesson items to seed the deck with on creation. */
        public readonly array $lessonItemIds = [],
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $name = (string) $request->string('name');

        return new static(
            userId: $request->filled('user_id') ? (int) $request->input('user_id') : null,
            moduleId: $request->filled('learning_module_id') ? (int) $request->input('learning_module_id') : null,
            name: $name,
            slug: $request->filled('slug') ? Str::slug((string) $request->string('slug')) : Str::slug($name),
            description: $request->input('description'),
            color: (string) $request->input('color', 'indigo'),
            icon: $request->input('icon'),
            isPublic: $request->boolean('is_public'),
            isActive: $request->boolean('is_active', true),
            lessonItemIds: array_map('intval', (array) $request->input('lesson_items', [])),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'learning_module_id' => $this->moduleId,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'is_public' => $this->isPublic,
            'is_active' => $this->isActive,
        ];
    }
}
