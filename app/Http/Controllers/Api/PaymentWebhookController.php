<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\CheckoutService;
use App\Services\Payment\TripayGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Tripay's payment-status callback. Unauthenticated by necessity (a webhook
 * carries no user session) — verified instead via HMAC-SHA256 over the raw
 * request body, using the merchant's private key (see
 * TripayGateway::verifyWebhookSignature()).
 *
 * Tripay expects exactly `{"success": true}` back, or it retries the
 * callback every 2 minutes up to 3 times.
 */
class PaymentWebhookController extends Controller
{
    public function __construct(
        private readonly TripayGateway $gateway,
        private readonly CheckoutService $checkout,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $signature = (string) $request->header('X-Callback-Signature');

        abort_unless($this->gateway->verifyWebhookSignature($request->getContent(), $signature), 401);

        $reference = (string) $request->input('reference');
        $status = (string) $request->input('status');

        abort_if($reference === '' || $status === '', 422);

        $this->checkout->handleGatewayCallback($reference, $status);

        return response()->json(['success' => true]);
    }
}
