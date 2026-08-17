<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin wrapper around Tripay's QRIS (closed payment) REST API.
 * Free sandbox, no business entity required: https://tripay.co.id/developer
 *
 * Unlike `GeminiClient` (where "unavailable" just disables a nice-to-have
 * feature), a failure here is real money — every method throws a clear
 * `RuntimeException` rather than degrading silently. The caller
 * (`CheckoutService`) decides what "not configured yet" should look like;
 * this class never pretends a failed/missing call succeeded.
 */
class TripayGateway
{
    public function __construct(
        private readonly ?string $merchantCode,
        private readonly ?string $apiKey,
        private readonly ?string $privateKey,
        private readonly bool $sandbox = true,
        private readonly string $method = 'QRIS2',
    ) {}

    public function available(): bool
    {
        return filled($this->merchantCode) && filled($this->apiKey) && filled($this->privateKey);
    }

    /**
     * @return array{reference: string, checkout_url: ?string, qr_url: ?string, status: string, expired_at: ?int}
     */
    public function createTransaction(Order $order, User $user): array
    {
        if (! $this->available()) {
            throw new RuntimeException('Tripay is not configured (missing merchant code/API key/private key).');
        }

        $signature = hash_hmac('sha256', $this->merchantCode.$order->reference.$order->amount, $this->privateKey);

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->asForm()
            ->post("{$this->baseUrl()}/transaction/create", [
                'method' => $this->method,
                'merchant_ref' => $order->reference,
                'amount' => $order->amount,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone ?: '0800000000',
                'order_items' => [[
                    'sku' => (string) $order->learning_module_id,
                    'name' => $order->learningModule->name,
                    'price' => $order->amount,
                    'quantity' => 1,
                ]],
                'signature' => $signature,
            ]);

        $body = $response->json();

        if (! $response->successful() || ! data_get($body, 'success')) {
            throw new RuntimeException('Tripay rejected the transaction: '.data_get($body, 'message', 'unknown error'));
        }

        $data = data_get($body, 'data', []);

        return [
            'reference' => (string) data_get($data, 'reference'),
            'checkout_url' => data_get($data, 'checkout_url'),
            'qr_url' => data_get($data, 'qr_url'),
            'status' => (string) data_get($data, 'status', 'UNPAID'),
            'expired_at' => data_get($data, 'expired_time'),
        ];
    }

    /** @return array{status: string} */
    public function fetchTransactionDetail(string $gatewayReference): array
    {
        if (! $this->available()) {
            throw new RuntimeException('Tripay is not configured (missing merchant code/API key/private key).');
        }

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->get("{$this->baseUrl()}/transaction/detail", ['reference' => $gatewayReference]);

        $body = $response->json();

        if (! $response->successful() || ! data_get($body, 'success')) {
            throw new RuntimeException('Failed to fetch Tripay transaction status: '.data_get($body, 'message', 'unknown error'));
        }

        return ['status' => (string) data_get($body, 'data.status', 'UNPAID')];
    }

    public function verifyWebhookSignature(string $rawBody, string $signature): bool
    {
        if (! $this->available() || $signature === '') {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $rawBody, $this->privateKey), $signature);
    }

    private function baseUrl(): string
    {
        return $this->sandbox ? 'https://tripay.co.id/api-sandbox' : 'https://tripay.co.id/api';
    }
}
