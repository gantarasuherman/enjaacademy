<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class AchievementData extends BaseData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $slug,
        public readonly ?string $description,
        public readonly ?string $icon,
        public readonly string $badgeColor,
        public readonly string $criteriaType,
        public readonly int $criteriaValue,
        public readonly int $xpReward,
        public readonly int $sortOrder,
        public readonly bool $isActive,
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $name = (string) $request->string('name');

        return new static(
            name: $name,
            slug: $request->filled('slug') ? Str::slug((string) $request->string('slug')) : Str::slug($name),
            description: $request->input('description'),
            icon: $request->input('icon'),
            badgeColor: (string) $request->input('badge_color', 'amber'),
            criteriaType: (string) $request->input('criteria_type', 'xp_total'),
            criteriaValue: (int) $request->input('criteria_value', 0),
            xpReward: (int) $request->input('xp_reward', 0),
            sortOrder: (int) $request->input('sort_order', 0),
            isActive: $request->boolean('is_active'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'badge_color' => $this->badgeColor,
            'criteria_type' => $this->criteriaType,
            'criteria_value' => $this->criteriaValue,
            'xp_reward' => $this->xpReward,
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
        ];
    }
}
