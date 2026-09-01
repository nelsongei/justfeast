<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaDarajaService
{
    protected string $env;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $shortcode;
    protected string $passkey;
    protected string $callbackUrl;
    protected string $transactionType;

    public function __construct()
    {
        $this->env             = config('mpesa.env', 'sandbox');
        $this->consumerKey     = config('mpesa.consumer_key', '');
        $this->consumerSecret  = config('mpesa.consumer_secret', '');
        $this->shortcode       = config('mpesa.shortcode', '174379');
        $this->passkey         = config('mpesa.passkey', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');
        $callback = config('mpesa.callback_url');
        if (empty($callback) || str_contains($callback, 'localhost') || str_contains($callback, '127.0.0.1') || !str_starts_with($callback, 'https://')) {
            $callback = 'https://zoyswfjujvc7.shares.zrok.io/api/mpesa/callback';
        }
        $this->callbackUrl     = $callback;
        $this->transactionType = config('mpesa.transaction_type', 'CustomerPayBillOnline');
    }

    /**
     * Get the base API URL depending on environment.
     */
    public function getBaseUrl(): string
    {
        return $this->env === 'live'
            ? rtrim(config('mpesa.live_base_url', 'https://api.safaricom.co.ke'), '/')
            : rtrim(config('mpesa.sandbox_base_url', 'https://sandbox.safaricom.co.ke'), '/');
    }

    /**
     * Generate or fetch cached M-Pesa OAuth Access Token.
     */
    public function getAccessToken(): ?string
    {
        $cacheKey = 'mpesa_access_token_' . $this->env;
        $cachedToken = Cache::get($cacheKey);

        if ($cachedToken && is_string($cachedToken)) {
            return $cachedToken;
        }

        try {
            $url = $this->getBaseUrl() . '/oauth/v1/generate?grant_type=client_credentials';

            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->acceptJson()
                ->get($url);

            if ($response->successful() && isset($response->json()['access_token'])) {
                $token = $response->json()['access_token'];
                Cache::put($cacheKey, $token, 3300);
                return $token;
            }

            Log::error('M-Pesa Daraja OAuth Failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('M-Pesa Daraja OAuth Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Initiate an M-Pesa STK Push (Lipa Na M-Pesa Online).
     *
     * @param  Order  $order
     * @param  string $phone  Phone in international format, e.g. 254712345678
     * @return array          ['success' => bool, 'checkout_request_id' => string|null, 'merchant_request_id' => string|null, 'message' => string]
     */
    public function initiateStkPush(Order $order, string $phone): array
    {
        $phone = self::formatPhone($phone);
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'success'             => false,
                'checkout_request_id' => null,
                'merchant_request_id' => null,
                'message'             => 'Failed to authenticate with M-Pesa. Please check M-Pesa API credentials.',
            ];
        }

        $timestamp = date('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);
    //    $amount    = (int) ceil($order->total_amount);
        $amount    = 1;

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => $this->transactionType,
            'Amount'            => $amount,
            'PartyA'            => $phone,
            'PartyB'            => $this->shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $this->callbackUrl,
            'AccountReference'  => 'Order#' . $order->id,
            'TransactionDesc'   => 'Payment for Order #' . $order->id,
        ];

        Log::info('M-Pesa STK Push Request', [
            'order_id'     => $order->id,
            'amount'       => $amount,
            'phone_masked' => substr($phone, 0, 6) . '****',
        ]);

        try {
            $url = $this->getBaseUrl() . '/mpesa/stkpush/v1/processrequest';

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($url, $payload);

            $body = $response->json();

            Log::info('M-Pesa STK Push Response', [
                'order_id'            => $order->id,
                'status'              => $response->status(),
                'response_code'       => $body['ResponseCode'] ?? null,
                'checkout_request_id' => $body['CheckoutRequestID'] ?? null,
            ]);

            if ($response->successful() && isset($body['ResponseCode']) && (string) $body['ResponseCode'] === '0') {
                return [
                    'success'             => true,
                    'checkout_request_id' => $body['CheckoutRequestID'] ?? null,
                    'merchant_request_id' => $body['MerchantRequestID'] ?? null,
                    'message'             => $body['CustomerMessage'] ?? 'STK Push prompt sent to phone.',
                ];
            }

            $errorMessage = $body['errorMessage'] ?? ($body['ResponseDescription'] ?? 'Failed to send STK Push.');

            return [
                'success'             => false,
                'checkout_request_id' => null,
                'merchant_request_id' => null,
                'message'             => $errorMessage,
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push Exception', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            return [
                'success'             => false,
                'checkout_request_id' => null,
                'merchant_request_id' => null,
                'message'             => 'Network error connecting to M-Pesa. Please try again.',
            ];
        }
    }

    /**
     * Query status of an STK Push transaction.
     *
     * @param  string $checkoutRequestId
     * @return array  ['success' => bool, 'result_code' => int|string|null, 'result_desc' => string|null, 'data' => array]
     */
    public function queryStkPushStatus(string $checkoutRequestId): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'success'     => false,
                'result_code' => null,
                'result_desc' => 'Failed to obtain OAuth access token.',
                'data'        => [],
            ];
        }

        $timestamp = date('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        try {
            $url = $this->getBaseUrl() . '/mpesa/stkpushquery/v1/query';

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($url, $payload);

            $body = $response->json();

            Log::info('M-Pesa STK Push Query Response', [
                'checkout_request_id' => $checkoutRequestId,
                'status'              => $response->status(),
                'result_code'         => $body['ResultCode'] ?? null,
            ]);

            if ($response->successful() && isset($body['ResultCode'])) {
                return [
                    'success'     => true,
                    'result_code' => $body['ResultCode'],
                    'result_desc' => $body['ResultDesc'] ?? '',
                    'data'        => $body,
                ];
            }

            return [
                'success'     => false,
                'result_code' => $body['ResultCode'] ?? null,
                'result_desc' => $body['errorMessage'] ?? ($body['ResultDesc'] ?? 'Query failed.'),
                'data'        => $body ?? [],
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push Query Exception', [
                'checkout_request_id' => $checkoutRequestId,
                'error'               => $e->getMessage(),
            ]);

            return [
                'success'     => false,
                'result_code' => null,
                'result_desc' => $e->getMessage(),
                'data'        => [],
            ];
        }
    }

    /**
     * Format Kenyan phone number to 2547XXXXXXXX / 2541XXXXXXXX format.
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '254' . substr($phone, 1);
        }

        if ((str_starts_with($phone, '7') || str_starts_with($phone, '1')) && strlen($phone) === 9) {
            return '254' . $phone;
        }

        return $phone;
    }
}
