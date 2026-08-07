<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class LearningModuleData extends BaseData
{
    public function __construct(
        public readonly int $languageId,
        public readonly string $name,
        public readonly ?string $slug,
        public readonly ?string $icon,
        public readonly string $color,
        public readonly string $contentType,
        public readonly ?string $permissionPrefix,
        public readonly ?string $description,
        public readonly int $sortOrder,
        public readonly bool $isActive,
        public readonly bool $isFeatured,
        /** Generate `{prefix}.{action}` permissions when saving. */
        public readonly bool $generatePermissions = false,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $name = (string) $request->string('name');

        return new static(
            languageId: (int) $request->input('language_id'),
            name: $name,
            slug: $request->filled('slug') ? Str::slug((string) $request->string('slug')) : Str::slug($name),
            icon: $request->input('icon'),
            color: (string) $request->input('color', 'indigo'),
            contentType: (string) $request->input('content_type', 'vocabulary'),
            permissionPrefix: $request->input('permission_prefix'),
            description: $request->input('description'),
            sortOrder: (int) $request->input('sort_order', 0),
            isActive: $request->boolean('is_active'),
            isFeatured: $request->boolean('is_featured'),
            generatePermissions: $request->boolean('generate_permissions'),
        );
    }

    public function toArray(): array
    {
        return [
            'language_id' => $this->languageId,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'color' => $this->color,
            'content_type' => $this->contentType,
            'permission_prefix' => $this->permissionPrefix ?: str_replace('-', '_', (string) $this->slug),
            'description' => $this->description,
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
            'is_featured' => $this->isFeatured,
        ];
    }
}
