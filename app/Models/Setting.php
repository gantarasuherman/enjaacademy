<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

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
            // API keys/tokens — stored via Crypt::encryptString(), never in plaintext.
            'secret' => $this->decryptSecret(),
            default => $this->value,
        };
    }

    private function decryptSecret(): ?string
    {
        if (blank($this->value)) {
            return null;
        }

        try {
            return Crypt::decryptString($this->value);
        } catch (DecryptException) {
            // Value predates encryption (e.g. seeded from plaintext) or the
            // app key rotated — treat as unset rather than crash the page.
            return null;
        }
    }
}
