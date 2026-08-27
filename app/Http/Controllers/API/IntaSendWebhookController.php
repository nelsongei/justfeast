<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IntaSendWebhookController extends Controller
{
    /**
     * Handle incoming IntaSend webhook payment notifications.
     * Enforces authenticity verification, idempotency tracking, currency validation,
     * amount precision match (bccomp), lockForUpdate transactions, and PII-sanitized logging.
     *
     * POST /api/intasend/webhook
     */
    public function handle(Request $request)
    {
        $invoiceId = $request->input('invoice_id');
        $state     = strtoupper($request->input('state', ''));
        $currency  = strtoupper($request->input('currency', 'KES'));
        $value     = $request->input('value');
        $apiRef    = $request->input('api_ref');

        // 1. Authenticity verification via challenge secret
        $expectedChallenge = config('intasend.challenge', env('INTASEND_CHALLENGE'));
        $receivedChallenge = $request->header('X-IntaSend-Challenge') ?: ($request->input('challenge') ?: '');

        if (!empty($expectedChallenge) && !hash_equals((string) $expectedChallenge, (string) $receivedChallenge)) {
            Log::warning('IntaSend Webhook: Authenticity challenge verification failed', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['status' => 'ignored', 'reason' => 'Invalid challenge token'], 200);
        }

        if (!$invoiceId || !$state) {
            return response()->json(['status' => 'ignored', 'reason' => 'Missing invoice_id or state'], 200);
        }

        // PII-Sanitized Logging: Log metadata only — zero phone numbers or customer data
        Log::info('IntaSend Webhook Received', [
            'invoice_id' => $invoiceId,
            'state'      => $state,
            'value'      => $value,
            'currency'   => $currency,
        ]);

        // 2. Idempotency Tracking — Prevent duplicate processing of the same invoice event
        $eventKey = $invoiceId . '_' . $state;

        $webhookEvent = PaymentWebhookEvent::firstOrCreate(
            ['event_key' => $eventKey],
            [
                'invoice_id' => $invoiceId,
                'state'      => $state,
                'status'     => 'processing',
            ]
        );

        if (!$webhookEvent->wasRecentlyCreated) {
            Log::info('IntaSend Webhook: Duplicate event key skipped for idempotency', ['event_key' => $eventKey]);
            return response()->json(['status' => 'ignored', 'reason' => 'Duplicate webhook event'], 200);
        }

        // 3. Process Payment States
        if ($state === 'COMPLETE') {
            if ($currency !== 'KES') {
                $webhookEvent->update(['status' => 'failed']);
                Log::warning('IntaSend Webhook: Rejected non-KES currency payment', [
                    'invoice_id' => $invoiceId,
                    'currency'   => $currency,
                ]);
                return response()->json(['status' => 'ignored', 'reason' => 'Invalid currency'], 200);
            }

            try {
                DB::transaction(function () use ($invoiceId, $apiRef, $value) {
                    // Query order by intasend_invoice_id (or fallback api_ref) with lockForUpdate
                    $query = Order::query()->lockForUpdate();

                    if ($invoiceId) {
                        $order = $query->where('intasend_invoice_id', $invoiceId)->first();
                    }

                    if (!$order && $apiRef && preg_match('/^order-(\d+)$/', $apiRef, $matches)) {
                        $order = Order::query()->lockForUpdate()->find((int) $matches[1]);
                    }

                    if (!$order) {
                        throw new \RuntimeException("Order not found for invoice_id {$invoiceId}");
                    }

                    // Already paid check
                    if ($order->payment_status === 'paid') {
                        return;
                    }

                    // Precise amount validation using bccomp
                    if (bccomp((string) $value, (string) $order->total_amount, 2) !== 0) {
                        throw new \RuntimeException("Payment amount mismatch: received {$value} vs order total {$order->total_amount}");
                    }

                    // Validated payment transition
                    $order->update([
                        'payment_status'    => 'paid',
                        'order_status'      => 'accepted',
                        'paid_at'           => now(),
                        'payment_reference' => $invoiceId,
                    ]);

                    event(new \App\Events\PaymentStatusUpdated($order->fresh(), 'paid', 'accepted'));
                });

                $webhookEvent->update(['status' => 'processed']);

                Log::info('IntaSend Webhook: Order payment verified and completed successfully', [
                    'invoice_id' => $invoiceId,
                    'value'      => $value,
                ]);

            } catch (\Exception $e) {
                $webhookEvent->update(['status' => 'failed']);
                Log::error('IntaSend Webhook Processing Error: ' . $e->getMessage(), ['invoice_id' => $invoiceId]);
                return response()->json(['status' => 'error', 'reason' => $e->getMessage()], 200);
            }

        } elseif ($state === 'FAILED') {
            try {
                DB::transaction(function () use ($invoiceId, $apiRef) {
                    $query = Order::query()->lockForUpdate();
                    $order = $invoiceId ? $query->where('intasend_invoice_id', $invoiceId)->first() : null;

                    if (!$order && $apiRef && preg_match('/^order-(\d+)$/', $apiRef, $matches)) {
                        $order = Order::query()->lockForUpdate()->find((int) $matches[1]);
                    }

                    if ($order && $order->payment_status === 'pending') {
                        $order->update(['payment_status' => 'failed']);
                    }
                });

                $webhookEvent->update(['status' => 'processed']);
            } catch (\Exception $e) {
                $webhookEvent->update(['status' => 'failed']);
            }
        }

        return response()->json(['status' => 'received'], 200);
    }
}
