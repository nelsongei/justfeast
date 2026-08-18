<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IntaSendWebhookController extends Controller
{
    /**
     * Handle incoming IntaSend webhook events.
     *
     * IntaSend sends a POST request to this endpoint whenever a payment state changes.
     * States: PENDING → PROCESSING → COMPLETE | FAILED
     *
     * POST /api/intasend/webhook
     *
     * Payload example:
     * {
     *   "invoice_id": "BRZKGPR",
     *   "state": "COMPLETE",
     *   "provider": "M-PESA",
     *   "charges": "0.00",
     *   "net_amount": "10.00",
     *   "currency": "KES",
     *   "value": "10.00",
     *   "account": "254712345678",
     *   "api_ref": "order-5",
     *   "challenge": "testnet",
     *   ...
     * }
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('IntaSend Webhook Received', $payload);

        // 1. Validate the challenge secret to ensure request is from IntaSend
        $expectedChallenge = config('intasend.challenge');

        if (!empty($expectedChallenge) && ($payload['challenge'] ?? '') !== $expectedChallenge) {
            Log::warning('IntaSend Webhook: Invalid challenge', [
                'received'  => $payload['challenge'] ?? 'none',
                'expected'  => $expectedChallenge,
            ]);

            // Return 200 to prevent IntaSend from retrying, but do nothing
            return response()->json(['status' => 'ignored', 'reason' => 'Invalid challenge'], 200);
        }

        $state  = $payload['state'] ?? null;
        $apiRef = $payload['api_ref'] ?? null;

        if (!$apiRef) {
            Log::warning('IntaSend Webhook: Missing api_ref', $payload);
            return response()->json(['status' => 'ignored', 'reason' => 'Missing api_ref'], 200);
        }

        // 2. Find the order by api_ref (format: "order-{id}")
        if (!preg_match('/^order-(\d+)$/', $apiRef, $matches)) {
            Log::warning('IntaSend Webhook: api_ref does not match expected format', ['api_ref' => $apiRef]);
            return response()->json(['status' => 'ignored', 'reason' => 'Unrecognised api_ref format'], 200);
        }

        $orderId = (int) $matches[1];
        $order   = Order::find($orderId);

        if (!$order) {
            Log::warning('IntaSend Webhook: Order not found', ['order_id' => $orderId]);
            return response()->json(['status' => 'ignored', 'reason' => 'Order not found'], 200);
        }

        // 3. Handle state transitions
        switch ($state) {
            case 'COMPLETE':
                if ($order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'order_status'   => 'accepted', // Auto-move into vendor queue
                    ]);

                    Log::info('IntaSend Webhook: Order marked PAID', [
                        'order_id'   => $orderId,
                        'invoice_id' => $payload['invoice_id'] ?? null,
                        'amount'     => $payload['value'] ?? null,
                    ]);
                }
                break;

            case 'FAILED':
                if ($order->payment_status === 'pending') {
                    $order->update(['payment_status' => 'failed']);

                    Log::info('IntaSend Webhook: Order payment FAILED', [
                        'order_id'      => $orderId,
                        'failed_reason' => $payload['failed_reason'] ?? null,
                        'failed_code'   => $payload['failed_code'] ?? null,
                    ]);
                }
                break;

            case 'PENDING':
            case 'PROCESSING':
                // Informational only — no action needed, order already in pending state
                Log::info('IntaSend Webhook: Payment in progress', [
                    'order_id' => $orderId,
                    'state'    => $state,
                ]);
                break;

            default:
                Log::info('IntaSend Webhook: Unknown state received', ['state' => $state]);
        }

        // Always return 200 so IntaSend doesn't retry unnecessarily
        return response()->json(['status' => 'received'], 200);
    }
}
