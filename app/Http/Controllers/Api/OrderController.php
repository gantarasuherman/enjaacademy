<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Repositories\Contracts\LearningModuleRepositoryInterface;
use App\Services\Payment\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkout,
        private readonly LearningModuleRepositoryInterface $modules,
    ) {}

    /**
     * Confirms taking a course — free modules (or ones the user already paid
     * for) enroll immediately; paid ones return a Tripay QRIS transaction
     * (or a simulated fallback order if Tripay isn't configured yet).
     */
    public function checkout(Request $request, string $moduleSlug): JsonResponse
    {
        $module = $this->modules->findBySlug($moduleSlug);

        abort_if($module === null, 404);
        $this->authorize('study', $module);

        try {
            $result = $this->checkout->checkout($request->user(), $module);
        } catch (RuntimeException $e) {
            // A real gateway failure (bad credentials, channel disabled, Tripay
            // down, ...) must be visible, not silently swallowed into the
            // simulated fallback — see CheckoutService's class docblock.
            return response()->json(['message' => __('Failed to start payment: :error', ['error' => $e->getMessage()])], 502);
        }

        return response()->json([
            'data' => [
                'requires_payment' => $result['requires_payment'],
                'enrolled' => $result['enrolled'],
                'payment_mode' => $result['payment_mode'],
                'order' => $result['order'] ? new OrderResource($result['order']) : null,
            ],
        ]);
    }

    /** Dev/demo stand-in for Tripay — rejected for orders with a real gateway transaction. */
    public function simulatePayment(Request $request, Order $order): JsonResponse
    {
        $order = $this->checkout->simulatePayment($request->user(), $order);

        return response()->json([
            'data' => ['order' => new OrderResource($order), 'enrolled' => true],
        ]);
    }

    /** "Cek Status Pembayaran" — actively asks Tripay instead of waiting for a webhook. */
    public function checkStatus(Request $request, Order $order): JsonResponse
    {
        try {
            $order = $this->checkout->checkGatewayStatus($request->user(), $order);
        } catch (RuntimeException $e) {
            return response()->json(['message' => __('Failed to check payment status: :error', ['error' => $e->getMessage()])], 502);
        }

        return response()->json([
            'data' => ['order' => new OrderResource($order), 'enrolled' => $order->status === 'paid'],
        ]);
    }
}
