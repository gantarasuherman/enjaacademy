<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;

class PermissionData extends BaseData
{
    public function __construct(
        public readonly string $name,
        public readonly string $guardName,
        /** @var array<int, int> */
        public readonly array $roleIds = [],
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        return new static(
            name: (string) $request->string('name'),
            guardName: (string) $request->input('guard_name', config('auth.defaults.guard', 'web')),
            roleIds: array_map('intval', (array) $request->input('roles', [])),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'guard_name' => $this->guardName,
        ];
    }
}
