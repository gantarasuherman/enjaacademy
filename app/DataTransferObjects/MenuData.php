<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class MenuData extends BaseData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $slug,
        public readonly ?int $parentId,
        public readonly ?string $icon,
        public readonly ?string $routeName,
        public readonly ?string $routeParams,
        public readonly ?string $url,
        public readonly string $type,
        public readonly string $position,
        public readonly ?string $badge,
        public readonly ?string $badgeColor,
        public readonly ?string $permissionName,
        public readonly ?string $roleDefault,
        public readonly string $target,
        public readonly bool $isVisible,
        public readonly bool $isActive,
        public readonly bool $isSidebar,
        public readonly bool $isTopbar,
        public readonly bool $isFooter,
        public readonly ?int $sortOrder,
        public readonly ?string $description,
        /** @var array<int, int> */
        public readonly array $roleIds = [],
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $position = (string) $request->input('position', 'sidebar');

        return new static(
            title: (string) $request->string('title'),
            slug: $request->filled('slug')
                ? Str::slug((string) $request->string('slug'))
                : Str::slug((string) $request->string('title')),
            parentId: $request->filled('parent_id') ? (int) $request->input('parent_id') : null,
            icon: $request->input('icon'),
            routeName: $request->input('route_name'),
            routeParams: $request->input('route_params'),
            url: $request->input('url'),
            type: (string) $request->input('type', 'menu'),
            position: $position,
            badge: $request->input('badge'),
            badgeColor: $request->input('badge_color'),
            permissionName: $request->input('permission_name'),
            roleDefault: $request->input('role_default'),
            target: (string) $request->input('target', '_self'),
            isVisible: $request->boolean('is_visible'),
            isActive: $request->boolean('is_active'),
            // The position radio is authoritative; the three booleans let one
            // menu appear in more than one bar when explicitly ticked.
            isSidebar: $request->boolean('is_sidebar') || $position === 'sidebar',
            isTopbar: $request->boolean('is_topbar') || $position === 'topbar',
            isFooter: $request->boolean('is_footer') || $position === 'footer',
            sortOrder: $request->filled('sort_order') ? (int) $request->input('sort_order') : null,
            description: $request->input('description'),
            roleIds: array_map('intval', (array) $request->input('roles', [])),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'parent_id' => $this->parentId,
            'icon' => $this->icon,
            'route_name' => $this->routeName,
            'route_params' => $this->routeParams,
            'url' => $this->url,
            'type' => $this->type,
            'position' => $this->position,
            'badge' => $this->badge,
            'badge_color' => $this->badgeColor,
            'permission_name' => $this->permissionName,
            'role_default' => $this->roleDefault,
            'target' => $this->target,
            'is_visible' => $this->isVisible,
            'is_active' => $this->isActive,
            'is_sidebar' => $this->isSidebar,
            'is_topbar' => $this->isTopbar,
            'is_footer' => $this->isFooter,
            'sort_order' => $this->sortOrder,
            'description' => $this->description,
        ];
    }
}
