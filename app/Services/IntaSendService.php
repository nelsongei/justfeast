<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IntaSendService
{
    protected string $baseUrl;
    protected ?string $secretKey;
    protected ?string $publishableKey;

    public function __construct()
    {
        $this->baseUrl        = rtrim(config('intasend.base_url', 'https://payment.intasend.com'), '/');
        $this->secretKey      = config('intasend.secret_key');
        $this->publishableKey = config('intasend.publishable_key');
    }

    /**
     * Initiate an M-Pesa STK Push via IntaSend.
     *
     * @param  Order  $order
     * @param  string $phone       Phone in international format, e.g. 254712345678
     * @param  string $email       Customer email
     * @param  string $firstName   Customer first name
     * @param  string $lastName    Customer last name
     * @return array               ['success' => bool, 'invoice_id' => string|null, 'api_ref' => string|null, 'message' => string]
     */
    public function initiateSTKPush(Order $order, string $phone, string $email = 'customer@glovopro.com', string $firstName = 'Customer', string $lastName = 'Order'): array
    {
        $apiRef = 'order-' . $order->id;

        $payload = [
            'first_name'   => $firstName,
            'last_name'    => $lastName,
            'email'        => $email,
            'host'         => config('app.url'),
            'amount'       => (int) $order->total_amount,
            'phone_number' => $phone,
            'currency'     => 'KES',
            'api_ref'      => $apiRef,
            'narrative'    => 'Payment for Order #' . $order->id,
        ];

        Log::info('IntaSend STK Push Request', [
            'order_id'     => $order->id,
            'amount'       => $order->total_amount,
            'phone_masked' => substr($phone, 0, 6) . '****',
        ]);

        try {
            $response = Http::withToken($this->secretKey)
                ->acceptJson()
                ->post("{$this->baseUrl}/api/v1/payment/mpesa-stk-push/", $payload);

            $body = $response->json();

            Log::info('IntaSend STK Push Response', [
                'order_id'   => $order->id,
                'status'     => $response->status(),
                'invoice_id' => $body['invoice']['invoice_id'] ?? null,
            ]);

            if ($response->successful() && isset($body['invoice']['invoice_id'])) {
                return [
                    'success'    => true,
                    'invoice_id' => $body['invoice']['invoice_id'],
                    'api_ref'    => $apiRef,
                    'message'    => 'STK Push sent successfully via IntaSend.',
                ];
            }

            // Extract a readable error message from the response
            $errorMessage = $body['detail'] ?? ($body['message'] ?? 'Failed to initiate STK Push via IntaSend. Please try again.');

            return [
                'success'    => false,
                'invoice_id' => null,
                'api_ref'    => null,
                'message'    => $errorMessage,
            ];

        } catch (\Exception $e) {
            Log::error('IntaSend STK Push Exception', ['order_id' => $order->id, 'error' => $e->getMessage()]);

            return [
                'success'    => false,
                'invoice_id' => null,
                'api_ref'    => null,
                'message'    => 'A network error occurred while contacting the payment provider. Please try again.',
            ];
        }
    }

    /**
     * Check the current payment status of an IntaSend invoice.
     *
     * @param  string $invoiceId   The invoice_id returned from initiateSTKPush
     * @return array               ['success' => bool, 'state' => string|null, 'data' => array]
     */
    public function checkPaymentStatus(string $invoiceId): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->acceptJson()
                ->post("{$this->baseUrl}/api/v1/payment/status/", [
                    'invoice_id' => $invoiceId,
                ]);

            $body = $response->json();

            Log::info('IntaSend Status Check', ['invoice_id' => $invoiceId, 'body' => $body]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'state'   => $body['invoice']['state'] ?? null,
                    'data'    => $body,
                ];
            }

            return [
                'success' => false,
                'state'   => null,
                'data'    => $body,
            ];

        } catch (\Exception $e) {
            Log::error('IntaSend Status Check Exception', ['invoice_id' => $invoiceId, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'state'   => null,
                'data'    => [],
            ];
        }
    }

    /**
     * Format a Kenyan phone number to the international format expected by IntaSend.
     * Accepts: 07XXXXXXXX, +2547XXXXXXXX, 2547XXXXXXXX
     *
     * @param  string $phone
     * @return string   e.g. 254712345678
     */
    public static function formatPhone(string $phone): string
    {
        // Strip all non-digit characters
        $phone = preg_replace('/\D/', '', $phone);

        // If starts with 0 (local format), replace with 254
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        return $phone;
    }
}
