<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Foundation\Http\FormRequest;

/**
 * DTOs carry validated input from the HTTP layer into services, so services
 * never touch a Request and stay callable from console commands and tests.
 */
abstract class BaseData
{
    abstract public static function fromRequest(FormRequest $request): static;

    /** @return array<string, mixed> Attributes ready for mass assignment. */
    abstract public function toArray(): array;

    /** Drops nulls so a partial update does not blank untouched columns. */
    protected function withoutNulls(array $attributes): array
    {
        return array_filter($attributes, static fn ($value) => $value !== null);
    }
}
