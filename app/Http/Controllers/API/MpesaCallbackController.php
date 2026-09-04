<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MpesaCallbackController extends Controller
{
    /**
     * Handle incoming Safaricom M-Pesa STK Push Callback notifications.
     * Enforces idempotency tracking, amount precision verification (bccomp),
     * lockForUpdate DB transactions, and status event broadcasting.
     *
     * POST /api/mpesa/callback
     */
    public function handle(Request $request)
    {
        $stkCallback = $request->input('Body.stkCallback');

        if (!$stkCallback) {
            Log::warning('M-Pesa Callback: Invalid or missing stkCallback body', ['ip' => $request->ip()]);
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;
        $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
        $resultCode        = $stkCallback['ResultCode'] ?? null;
        $resultDesc        = $stkCallback['ResultDesc'] ?? '';

        Log::info('M-Pesa Callback Received', [
            'checkout_request_id' => $checkoutRequestId,
            'merchant_request_id' => $merchantRequestId,
            'result_code'         => $resultCode,
            'result_desc'         => $resultDesc,
        ]);

        if (!$checkoutRequestId) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        // 1. Idempotency Check — Prevent duplicate processing of callback
        $eventKey = $checkoutRequestId . '_' . $resultCode;

        $webhookEvent = PaymentWebhookEvent::firstOrCreate(
            ['event_key' => $eventKey],
            [
                'invoice_id' => $checkoutRequestId,
                'state'      => (string) $resultCode,
                'status'     => 'processing',
            ]
        );

        if (!$webhookEvent->wasRecentlyCreated) {
            Log::info('M-Pesa Callback: Duplicate event key skipped for idempotency', ['event_key' => $eventKey]);
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        // Parse CallbackMetadata array if present
        $metadata = [];
        if (isset($stkCallback['CallbackMetadata']['Item'])) {
            foreach ($stkCallback['CallbackMetadata']['Item'] as $item) {
                if (isset($item['Name'])) {
                    $metadata[$item['Name']] = $item['Value'] ?? null;
                }
            }
        }

        $amount             = $metadata['Amount'] ?? null;
        $mpesaReceiptNumber = $metadata['MpesaReceiptNumber'] ?? null;
        $phoneNumber        = $metadata['PhoneNumber'] ?? null;

        // 2. Process Successful Payment (ResultCode === 0)
        if ((int) $resultCode === 0) {
            try {
                DB::transaction(function () use ($checkoutRequestId, $merchantRequestId, $amount, $mpesaReceiptNumber) {
                    $orders = Order::query()
                        ->lockForUpdate()
                        ->where('mpesa_checkout_request_id', $checkoutRequestId)
                        ->orWhere('mpesa_checkout_request_id', 'LIKE', "{$checkoutRequestId}-batch-%")
                        ->orWhere('mpesa_merchant_request_id', $merchantRequestId)
                        ->get();

                    if ($orders->isEmpty()) {
                        throw new \RuntimeException("Order not found for checkout_request_id {$checkoutRequestId}");
                    }

                    $batchTotal  = (float) $orders->sum('total_amount');
                    $expectedInt = (int) ceil($batchTotal);

                    if ($amount !== null && (int) $amount !== $expectedInt && bccomp((string) $amount, (string) $batchTotal, 2) !== 0) {
                        throw new \RuntimeException("Payment amount mismatch: received {$amount} vs expected {$expectedInt} / {$batchTotal}");
                    }

                    foreach ($orders as $order) {
                        if ($order->payment_status !== 'paid') {
                            $order->update([
                                'payment_status'       => 'paid',
                                'order_status'         => 'accepted',
                                'paid_at'              => now(),
                                'payment_reference'    => $mpesaReceiptNumber ?: $checkoutRequestId,
                                'mpesa_receipt_number' => $mpesaReceiptNumber,
                            ]);

                            event(new \App\Events\PaymentStatusUpdated($order->fresh(), 'paid', 'accepted'));
                        }
                    }
                });

                $webhookEvent->update(['status' => 'processed']);

                Log::info('M-Pesa Callback: Order payment verified and completed successfully', [
                    'checkout_request_id' => $checkoutRequestId,
                    'receipt_number'      => $mpesaReceiptNumber,
                    'amount'              => $amount,
                ]);

            } catch (\Exception $e) {
                $webhookEvent->update(['status' => 'failed']);
                Log::error('M-Pesa Callback Error: ' . $e->getMessage(), ['checkout_request_id' => $checkoutRequestId]);
            }

        } else {
            // ResultCode != 0: Transaction cancelled / failed / timed out
            try {
                DB::transaction(function () use ($checkoutRequestId, $merchantRequestId) {
                    $orders = Order::query()
                        ->lockForUpdate()
                        ->where('mpesa_checkout_request_id', $checkoutRequestId)
                        ->orWhere('mpesa_checkout_request_id', 'LIKE', "{$checkoutRequestId}-batch-%")
                        ->orWhere('mpesa_merchant_request_id', $merchantRequestId)
                        ->with('items.product')
                        ->get();

                    foreach ($orders as $order) {
                        if ($order->payment_status === 'pending') {
                            $order->update(['payment_status' => 'failed']);

                            // Restore stock
                            foreach ($order->items as $item) {
                                if ($item->product) {
                                    $item->product->increment('stock_quantity', $item->quantity);
                                    if ($item->product->fresh()->stock_quantity > 0) {
                                        $item->product->update(['stock_status' => 'in_stock']);
                                    }
                                }
                            }
                        }
                    }
                });

                $webhookEvent->update(['status' => 'processed']);
            } catch (\Exception $e) {
                $webhookEvent->update(['status' => 'failed']);
            }
        }

        // Return Safaricom standard acknowledgement
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}
