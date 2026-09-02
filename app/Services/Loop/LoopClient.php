<?php

namespace App\Services\Loop;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoopClient
{
    public function __construct(private readonly LoopTokenProvider $tokens) {}

    /**
     * Send LOOP M-Pesa Prompt request (NEO_MRCHNT_STK) with HMAC-SHA256 payload signature.
     */
    public function sendMpesaPrompt(PaybillPaymentData $data, string $customerPhone): array
    {
        $tillNo        = config('loop.till_number', '133238');
        $signingSecret = config('loop.signing_secret', 'hyqd7bwMr9Kv-C5PW4n7uF4TiMnMp_hyvyhYYkYlcU8');
        $url           = config('loop.base_url') . config('loop.prompt_path');

        $timestamp     = now('UTC')->format('Y-m-d\TH:i:s\Z');
        $nonce         = strtolower((string) Str::uuid());
        $txnReference  = strtolower((string) Str::uuid());

        // Canonical string: tillNo|timestamp|nonce (pipe-joined, no whitespace)
        $canonicalString = implode('|', [
            (string) $tillNo,
            $timestamp,
            $nonce,
        ]);

        $signature = hash_hmac('sha256', $canonicalString, $signingSecret);

        $phoneFormatted = self::formatLoopPhone($customerPhone);

        $payload = [
            'serviceCode'  => 'NEO_MRCHNT_STK',
            'txnReference' => $txnReference,
            'requestParameters' => [
                'tillNo'      => (string) $tillNo,
                'payMblNo'    => $phoneFormatted,
                'amount'      => (string) ((int) ceil((float) $data->amount)),
                'extRefNo'    => $data->merchantReference,
                'callBackUrl' => config('loop.callback_url'),
                'timestamp'   => $timestamp,
                'nonce'       => $nonce,
                'signature'   => $signature,
            ],
        ];

        $accessToken = $this->tokens->token();

        Log::info('LOOP M-Pesa Prompt (NEO_MRCHNT_STK) Dispatching', [
            'merchant_reference' => $data->merchantReference,
            'till_no'            => $tillNo,
            'phone'              => $phoneFormatted,
            'amount'             => $data->amount,
            'url'                => $url,
            'has_token'          => filled($accessToken),
            'token_prefix'       => filled($accessToken) ? substr($accessToken, 0, 6) . '...' : null,
        ]);

        try {
            $headers = [];
            if ($apiKey = config('loop.api_key')) {
                $headers['ApiKey'] = $apiKey;
            }

            // Send request with exact headers specified by LOOP: Authorization: Bearer <token>, Content-Type: application/json, and ApiKey
            $response = Http::acceptJson()
                ->connectTimeout(config('loop.connect_timeout'))
                ->timeout(config('loop.request_timeout'))
                ->withToken($accessToken)
                ->withHeaders($headers)
                ->post($url, $payload);

            if ($response->status() === 401) {
                $this->tokens->forget();
            }

            $json = $response->json();

            $statusCode = data_get($json, 'statusCode', $response->status());
            $message    = data_get($json, 'message', 'Prompt request sent');

            if ($response->successful() && (int) $statusCode === 200) {
                Log::info('LOOP M-Pesa Prompt Successful', [
                    'merchant_reference' => $data->merchantReference,
                    'status'             => $response->status(),
                    'body'               => $json,
                ]);

                return [
                    'success'                 => true,
                    'statusCode'              => 200,
                    'message'                 => $message,
                    'transactionId'           => data_get($json, 'data.txnReference', $txnReference),
                    'requestReference'        => data_get($json, 'data.requestReference'),
                    'serviceTransactionStatus'=> data_get($json, 'data.serviceTransactionStatus', 'PENDING'),
                    'raw'                     => $json,
                ];
            }

            Log::error('LOOP request failed', [
                'endpoint'         => $url,
                'http_status'      => $response->status(),
                'response'         => $json ?: $response->body(),
                'www_authenticate' => $response->header('WWW-Authenticate'),
                'has_token'        => filled($accessToken),
                'token_prefix'     => filled($accessToken) ? substr($accessToken, 0, 6) . '...' : null,
            ]);

            return [
                'success'    => false,
                'statusCode' => $statusCode,
                'message'    => $message ?: ('HTTP ' . $response->status() . ' error from LOOP gateway.'),
                'raw'        => $json ?? [],
            ];

        } catch (\Exception $e) {
            Log::error('LOOP M-Pesa Prompt Exception: ' . $e->getMessage());

            return [
                'success'    => false,
                'statusCode' => 500,
                'message'    => 'Network error connecting to LOOP gateway: ' . $e->getMessage(),
                'raw'        => [],
            ];
        }
    }

    /**
     * Dispatch Pay to Paybill request to LOOP API using MRCHNT_PAYMENTS serviceCode.
     */
    public function payToPaybill(PaybillPaymentData $data): array
    {
        $merchantTill  = config('loop.till_number', '133238');
        $signingSecret = config('loop.signing_secret', 'hyqd7bwMr9Kv-C5PW4n7uF4TiMnMp_hyvyhYYkYlcU8');
        $url           = config('loop.paybill_url', config('loop.base_url') . config('loop.paybill_path'));

        $timestamp    = now('UTC')->format('Y-m-d\TH:i:s\Z');
        $nonce        = strtolower((string) Str::uuid());
        $txnReference = strtolower((string) Str::uuid());

        $canonicalString = implode('|', [
            (string) $merchantTill,
            $timestamp,
            $nonce,
        ]);

        $signature = hash_hmac('sha256', $canonicalString, $signingSecret);

        $payload = [
            'serviceCode'  => 'MRCHNT_PAYMENTS',
            'txnReference' => $txnReference,
            'requestParameters' => [
                'merchantTill'    => (string) $merchantTill,
                'merchantRcvTill' => (string) $data->paybillNumber,
                'accountNumber'   => (string) $data->accountReference,
                'amount'          => (string) ((int) ceil((float) $data->amount)),
                'channel'         => 'LOOP',
                'timestamp'       => $timestamp,
                'nonce'           => $nonce,
                'signature'       => $signature,
            ],
        ];

        $accessToken = $this->tokens->token();

        Log::info('LOOP Pay to Paybill (MRCHNT_PAYMENTS) Dispatching', [
            'merchant_reference' => $data->merchantReference,
            'merchant_till'      => $merchantTill,
            'paybill_number'     => $data->paybillNumber,
            'account_number'     => $data->accountReference,
            'amount'             => $data->amount,
            'url'                => $url,
            'payload'            => $payload,
        ]);

        try {
            $headers = [];
            if ($apiKey = config('loop.api_key')) {
                $headers['ApiKey'] = $apiKey;
            }

            $response = Http::acceptJson()
                ->connectTimeout(config('loop.connect_timeout'))
                ->timeout(config('loop.request_timeout'))
                ->withToken($accessToken)
                ->withHeaders($headers)
                ->post($url, $payload);

            if ($response->status() === 401) {
                $this->tokens->forget();
            }

            $json = $response->json();

            $statusCode = data_get($json, 'statusCode', $response->status());
            $message    = data_get($json, 'message', 'Paybill payment request sent');

            if ($response->successful() && (int) $statusCode === 200) {
                $serviceTxnStatus = data_get($json, 'data.serviceTransactionStatus', 'PENDING');
                $rspCode          = data_get($json, 'data.response.responseDetails.rspCode');
                $isSuccess        = ($rspCode === 'OGW00000') || ($serviceTxnStatus === 'COMPLETED');

                Log::info('LOOP Pay to Paybill Successful', [
                    'merchant_reference' => $data->merchantReference,
                    'status'             => $response->status(),
                    'body'               => $json,
                ]);

                return [
                    'success'                 => $isSuccess,
                    'statusCode'              => 200,
                    'message'                 => $message,
                    'transactionId'           => data_get($json, 'data.txnReference', $txnReference),
                    'requestReference'        => data_get($json, 'data.requestReference'),
                    'serviceTransactionStatus'=> $serviceTxnStatus,
                    'rspCode'                 => $rspCode,
                    'raw'                     => $json,
                ];
            }

            Log::error('LOOP Paybill request failed', [
                'endpoint'    => $url,
                'http_status' => $response->status(),
                'response'    => $json ?: $response->body(),
            ]);

            return [
                'success'    => false,
                'statusCode' => $statusCode,
                'message'    => $message ?: ('HTTP ' . $response->status() . ' error from LOOP gateway.'),
                'raw'        => $json ?? [],
            ];

        } catch (\Exception $e) {
            Log::error('LOOP Pay to Paybill Exception: ' . $e->getMessage());

            return [
                'success'    => false,
                'statusCode' => 500,
                'message'    => 'Network error connecting to LOOP gateway: ' . $e->getMessage(),
                'raw'        => [],
            ];
        }
    }

    /**
     * Inquire merchant transaction status.
     */
    public function inquireTransaction(string $transactionId, string $merchantReference): array
    {
        $url         = $this->url(config('loop.inquiry_path'));
        $accessToken = $this->tokens->token();

        try {
            $response = Http::acceptJson()
                ->connectTimeout(config('loop.connect_timeout'))
                ->timeout(config('loop.request_timeout'))
                ->withToken($accessToken)
                ->get($url, [
                    'transactionId'     => $transactionId,
                    'merchantReference' => $merchantReference,
                ]);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            return [
                'status'  => 'UNKNOWN',
                'message' => 'Transaction inquiry returned HTTP ' . $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('LOOP Transaction Inquiry Exception: ' . $e->getMessage());
            return [
                'status'  => 'UNKNOWN',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number to 2547XXXXXXXX or 07XXXXXXXX format.
     */
    public static function formatLoopPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if ((str_starts_with($phone, '7') || str_starts_with($phone, '1')) && strlen($phone) === 9) {
            return '254' . $phone;
        }

        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '254' . substr($phone, 1);
        }

        return $phone;
    }

    private function request(): PendingRequest
    {
        return Http::acceptJson()
            ->connectTimeout(config('loop.connect_timeout'))
            ->timeout(config('loop.request_timeout'));
    }

    private function url(string $path): string
    {
        return config('loop.base_url') . '/' . ltrim($path, '/');
    }
}
