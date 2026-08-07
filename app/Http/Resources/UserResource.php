<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'avatar_url' => $this->avatar_url,
            'is_active' => $this->is_active,
            'email_verified' => $this->email_verified_at !== null,
            'locale' => $this->locale,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'permissions' => $this->when(
                $request->user()?->is($this->resource) ?? false,
                fn () => $this->getAllPermissions()->pluck('name'),
            ),
            'stat' => $this->whenLoaded('stat', fn () => [
                'level' => $this->stat->level,
                'xp_total' => $this->stat->xp_total,
                'xp_into_level' => $this->stat->xpIntoCurrentLevel(),
                'xp_for_next' => $this->stat->xpNeededForNextLevel(),
                'level_percent' => $this->stat->levelProgressPercent(),
                'streak_days' => $this->stat->streak_days,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
