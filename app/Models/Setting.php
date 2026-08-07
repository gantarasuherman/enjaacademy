<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'group', 'key', 'value', 'type', 'label', 'description', 'is_public', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    /** Cast the stored string back to its declared type. */
    public function typedValue(): mixed
    {
        return match ($this->type) {
            'bool' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'int' => (int) $this->value,
            'float' => (float) $this->value,
            'json', 'array' => json_decode((string) $this->value, true) ?? [],
            default => $this->value,
        };
    }
}
