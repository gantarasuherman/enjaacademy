<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\LearningModule;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use App\Services\Audit\AuditLogger;

/**
 * `simulatePayment()` is a dev/demo stand-in for Tripay's QRIS flow, used
 * only when `TripayGateway` isn't configured yet — see `checkout()`. Once a
 * real Tripay transaction exists on an order (`order->gateway === 'tripay'`),
 * simulating it is blocked (see the guard in `simulatePayment()`) so the
 * demo button can never bypass a real payment.
 */
class CheckoutService
{
    public function __construct(
        private readonly EnrollmentRepositoryInterface $enrollments,
        private readonly TripayGateway $gateway,
        private readonly AuditLogger $audit,
    ) {}

    /** Free modules are always "paid for"; paid ones need a settled order. */
    public function hasPaidAccess(User $user, LearningModule $module): bool
    {
        if (! $module->is_paid) {
            return true;
        }

        return Order::query()
            ->where('user_id', $user->id)
            ->where('learning_module_id', $module->id)
            ->where('status', 'paid')
            ->exists();
    }

    /**
     * @return array{requires_payment: bool, enrolled: bool, payment_mode: ?string, order: ?Order}
     */
    public function checkout(User $user, LearningModule $module): array
    {
        if ($this->hasPaidAccess($user, $module)) {
            $this->grantAccess($user, $module);

            return ['requires_payment' => false, 'enrolled' => true, 'payment_mode' => null, 'order' => null];
        }

        $order = Order::query()
            ->where('user_id', $user->id)
            ->where('learning_module_id', $module->id)
            ->where('status', 'pending')
            ->first();

        // A Tripay QR/checkout link that already expired can't be reused —
        // start a fresh order (and a fresh Tripay transaction) instead.
        if ($order && $order->expired_at !== null && $order->expired_at->isPast()) {
            $order->update(['status' => 'expired']);
            $order = null;
        }

        if (! $order) {
            $order = Order::create([
                'user_id' => $user->id,
                'learning_module_id' => $module->id,
                'amount' => $module->price ?? 0,
                'status' => 'pending',
            ]);
        }

        $order->setRelation('learningModule', $module);

        // Only ever attempted once per order — `gateway_reference` set means
        // Tripay already has this transaction, re-checkout just re-serves it.
        if ($this->gateway->available() && $order->gateway_reference === null) {
            $result = $this->gateway->createTransaction($order, $user);

            $order->update([
                'gateway' => 'tripay',
                'gateway_reference' => $result['reference'],
                'checkout_url' => $result['checkout_url'],
                'qr_url' => $result['qr_url'],
                'expired_at' => $result['expired_at'] ? now()->setTimestamp((int) $result['expired_at']) : null,
            ]);
        }

        return [
            'requires_payment' => true,
            'enrolled' => false,
            'payment_mode' => $order->gateway === 'tripay' ? 'tripay' : 'simulated',
            'order' => $order->fresh(['learningModule']),
        ];
    }

    /** Dev/demo stand-in for Tripay — only for orders with no real gateway transaction. */
    public function simulatePayment(User $user, Order $order): Order
    {
        abort_unless($order->user_id === $user->id, 403);
        abort_if($order->gateway !== null, 422, __('This order uses a real payment gateway — check its status instead of simulating.'));
        abort_unless($order->status === 'pending', 422, __('This order has already been processed.'));

        $order->update(['status' => 'paid', 'payment_method' => 'simulated', 'paid_at' => now()]);

        $this->grantAccess($user, $order->learningModule);

        $this->audit->event(
            'paid',
            __('Simulated payment for order :ref (:amount)', ['ref' => $order->reference, 'amount' => $order->amount]),
            $order,
        );

        return $order->fresh(['learningModule']);
    }

    /**
     * The "Cek Status Pembayaran" button — actively asks Tripay instead of
     * waiting for a webhook, since a local/unexposed server can never
     * receive one without a tunnel (ngrok/etc).
     */
    public function checkGatewayStatus(User $user, Order $order): Order
    {
        abort_unless($order->user_id === $user->id, 403);

        if ($order->status !== 'pending' || $order->gateway_reference === null) {
            return $order->fresh(['learningModule']);
        }

        $detail = $this->gateway->fetchTransactionDetail($order->gateway_reference);

        return $this->applyGatewayStatus($order, $detail['status']);
    }

    /** Lands here from `PaymentWebhookController` (Tripay's callback). */
    public function handleGatewayCallback(string $gatewayReference, string $status): Order
    {
        $order = Order::query()->with('learningModule')->where('gateway_reference', $gatewayReference)->firstOrFail();

        if ($order->status !== 'pending') {
            return $order;
        }

        return $this->applyGatewayStatus($order, $status);
    }

    /** Maps Tripay's UNPAID/PAID/FAILED/EXPIRED/REFUND onto our status column. */
    private function applyGatewayStatus(Order $order, string $tripayStatus): Order
    {
        $status = match ($tripayStatus) {
            'PAID' => 'paid',
            'EXPIRED' => 'expired',
            // No refund flow exists yet — treat a refund the same as a
            // failed payment rather than inventing a status nothing handles.
            'FAILED', 'REFUND' => 'failed',
            default => null, // still UNPAID — nothing to update
        };

        if ($status === null) {
            return $order;
        }

        $order->update(array_filter([
            'status' => $status,
            'payment_method' => $status === 'paid' ? 'gateway' : $order->payment_method,
            'paid_at' => $status === 'paid' ? now() : null,
        ], fn ($v) => $v !== null));

        if ($status === 'paid') {
            $this->grantAccess($order->user, $order->learningModule);
        }

        $this->audit->event('paid', __('Tripay status update for order :ref: :status', [
            'ref' => $order->reference,
            'status' => $tripayStatus,
        ]), $order);

        return $order->fresh(['learningModule']);
    }

    private function grantAccess(User $user, LearningModule $module): void
    {
        if (! $this->enrollments->isEnrolled($user, $module)) {
            $this->enrollments->toggle($user, $module);
        }
    }
}
