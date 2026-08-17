<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A checkout/payment record for a paid course. No real gateway is wired up
 * yet — `payment_method` is `'simulated'` (dev "Simulasikan Pembayaran
 * Berhasil" button) or `'gateway'` (via the generic webhook, unused today).
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'learning_module_id', 'amount', 'status', 'payment_method', 'paid_at',
        'gateway', 'gateway_reference', 'checkout_url', 'qr_url', 'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function learningModule(): BelongsTo
    {
        return $this->belongsTo(LearningModule::class);
    }

    /** Human-readable order number, e.g. "ORD-000123" — derived from `id`, never stored. */
    public function getReferenceAttribute(): string
    {
        return 'ORD-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
